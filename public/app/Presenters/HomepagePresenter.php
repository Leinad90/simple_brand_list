<?php

declare(strict_types=1);

namespace App\Presenters;

use app\Model\Brand;
use Nette;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{

    public function __construct(
        private readonly Brand $brand
    )
    {
    }

    public function actionEdit()
    {
        if($this->isAjax()) {
            $this->redrawControl();
        }
    }

    public function renderDefault(int $page=1)
    {
        $count = $this->brand->getCount();
        $paginator = new Nette\Utils\Paginator;
        $paginator->setItemCount($count);
        $paginator->setItemsPerPage(10);
        $paginator->setPage($page);

        $brands = $this->brand->getPage($paginator->getOffset(), $paginator->getLength());

        $this->template->brands = $brands;
        $this->template->paginator = $paginator;
    }

    public function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();
        $form->addText('name','Jméno');
        $form->addHidden('id');
        return $form;
    }
}
