<?php

declare(strict_types=1);
namespace App\Presenters;

use Nette;

/**
 * Class HomepagePresenter
 * @package App\Presenters
 */
final class HomepagePresenter extends Nette\Application\UI\Presenter{

  public function actionDefault():never {
    $this->redirect('Todo:default');
  }

}
