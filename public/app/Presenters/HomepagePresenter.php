<?php

declare(strict_types=1);

namespace App\Presenters;

use app\Model\Brand;
use app\Model\OrderBy;
use Nette;
use Nette\Application\Attributes\Persistent;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{

    #[Persistent]
    public int $page=1;

    #[Persistent]
    public int $items=10;

    #[Persistent]
    public string $orderBy='ASC';

    public function __construct(
        private readonly Brand $brand
    )
    {
        parent::__construct();
    }

    public function renderDefault() : void
    {
        $orderByObj = OrderBy::tryFrom($this->orderBy)??OrderBy::ASC;
        $count = $this->brand->getCount();
        $paginator = new Nette\Utils\Paginator();
        $paginator->setItemCount($count);
        $paginator->setItemsPerPage($this->items);
        $paginator->setPage($this->page);

        $brands = $this->brand->getPage($paginator->getOffset(), $paginator->getLength(),$orderByObj);

        $this->template->brands = $brands;
        $this->template->paginator = $paginator;
        $this->template->orderBy = $this->orderBy;
        if($this->isAjax()) {
            $this->redrawControl();
        }
    }

    public function save(Nette\Application\UI\Form $form, array $data): never
    {
        $this->brand->save($data);
        $this->flashMessage('Uloženo '.$data['name']);
        $this->redirect('default');
    }

    public function actionEdit(int $id = null): void
    {
        if($id)
        {
            $this->template->data = $data = $this->brand->getById($id);
            if($data===null) {
                $this->error('Značka nenalezena');
            }
            $this['form']->setDefaults($data);
        }
        $this->redrawControl();
    }

    public function actionDelete(int $id): never
    {
        $data = $this->brand->getById($id);
        $this->brand->delete($id);
        $this->flashMessage('Smazáno '.$data?->name);
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
