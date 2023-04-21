<?php

declare(strict_types=1);

namespace App\Presenters;

use app\Model\Brand;
use Nette;
use Nette\Application\Attributes\Persistent;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{

    #[Persistent]
    public int $page=1;

    #[Persistent]
    public int $items=10;

    public function __construct(
        private readonly Brand $brand
    )
    {
        parent::__construct();
    }

    public function renderDefault(int $page=null, int $items=null)
    {
        $this->page = $page??$this->page;
        $this->items = $items??$this->items;
        $count = $this->brand->getCount();
        $paginator = new Nette\Utils\Paginator;
        $paginator->setItemCount($count);
        $paginator->setItemsPerPage($this->items);
        $paginator->setPage($this->page);

        $brands = $this->brand->getPage($paginator->getOffset(), $paginator->getLength());

        $this->template->brands = $brands;
        $this->template->paginator = $paginator;
        if($this->isAjax()) {
            $this->redrawControl('table');
        }
    }

    public function save(Nette\Application\UI\Form $form, array $data): never
    {
        $this->brand->save($data);
        $this->flashMessage('Uloženo');
        $this->redirect('default');
    }

    public function actionEdit(int $id = null): void
    {
        if($id)
        {
            $data = $this->brand->getById($id);
            $this['form']->setDefaults($data);
        }
        $this->redrawControl('modal');
    }

    public function actionDelete(int $id): never
    {
        $this->brand->delete($id);
        $this->flashMessage('Smazáno');
        $this->redirect('default');
    }

    public function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();
        $form->addText('name','Jméno');
        $form->addHidden('id');
        $form->addSubmit('send','Uložit');
        $form->onSuccess[] = [$this, 'save'];
        return $form;
    }
}
