<?php

namespace App\Admin\Controllers;

use App\Diagnostic;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class DiagnosticController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Diagnostic';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Diagnostic());

        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            // $actions->disableView();
        });
        $grid->column('id', __('Id'));
        $grid->column('q-10', __('Age'))->filter('range');
        $grid->column('q-1', __('Fièvre'))->bool(
            [
                '1' => true,
                '2' => true,
                '3' => false,
                '4' => true,
                '5' => false
            ]
        )->filter([
            1 => 'Ne sais pas',
            2 => '39°C ou plus',
            3 => 'Entre 37,8°C et 38,9°C',
            4 => 'Moins de 35,5°C',
            5 => 'Pas de fièvre'
        ]);
        // $grid->column('q-2', __('Tempéranture'))->filter('range');
        $grid->column('q-2', __('Toux'))->bool();
        $grid->column('q-9', __('Soufle'))->bool(['1' => true, '0' => false])->filter([
            0 => 'Non',
            1 => 'oui',
        ]);
        $grid->column('q-4', __('Mal de gorge/courbatures'))->bool(['1' => true, '0' => false])->filter([
            0 => 'Non',
            1 => 'oui',
        ]);
        $grid->column('pronostique')->display(function () {
            $imc = $this['q-12'] / (($this['q-11'] / 100) ^ 2);
            if (
                $this['q-10'] >= 70 ||
                $imc >= 30 || $this['q-13'] == 1 ||
                $this['q-14'] == 1 ||
                $this['q-15'] == 1 || $this['q-16'] == 1 ||
                $this['q-17'] == 1 || $this['q-18'] == 1 ||
                $this['q-19'] == 1 || $this['q-20'] == 1 ||
                $this['q-21'] == 1
            ) {
                return true;
            }
            return false;
        })->bool()->filter([
            false => 'Non',
            true => 'oui',
        ]);
        $grid->column('majeurs')->display(function () {
            $r = 0;
            if ($this['q-8'] == 1)
                $r++;
            if ($this['q-9'] == 1)
                $r++;
            return $r;
        })->filter('range');

        $grid->column('mineurs')->display(function () {
            $r = 0;
            if ($this['q-1'] == 1 || $this['q-1'] == 2 || $this['q-1'] == 4) {
                $r++;
            }
            if ($this['q-7'] == 1) {
                $r++;
            }
            return $r;
        })->filter('range');

        // $grid->column('q-24', __('Région'))->filter('like');
        $grid->column('results_code', __('Results code'));
        $grid->column('duration',__('Durée'));
        $grid->column('created_at', __('Created at'));
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Diagnostic::findOrFail($id));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });

        $show->field('id', __('Id'));
        $show->{"q-1"}(__('Fièvre ces 48 dernières heures (frissons, sueurs) ?'))->as(function ($value) {
            $r = "";
            switch ($value) {
                case 1:
                    $r = "Ne sais pas";
                    break;
                case 2:
                    $r = "39°C ou plus";
                    break;
                case 3:
                    $r = "Entre 37,8°C et 38,9°C";
                    break;
                case 4:
                    $r = "Moins de 35,5°C";
                    break;
                case 5:
                    $r = "Pas de fièvre";
                    break;
            }
            return $r;
        });
        $show->field('q-2', __('Augmentation de votre toux habituelle '));
        $show->field('q-3', __('Anosmie'));
        $show->field('q-4', __('Mal de gorge et/ou des douleurs musculaires et/ou des courbatures inhabituelles'));
        $show->field('q-5', __('Ces dernières 24 heures, avez-vous de la diarrhée'));
        $show->field('q-6', __('Une fatigue inhabituelle'));
        $show->field('q-7', __('Alitement > 50% du temps diurne'));
        $show->field('q-8', __('Impossibilité de vous alimenter ou de boire DEPUIS 24 HEURES OU PLUS'));
        $show->field('q-9', __('Manque de souffle INHABITUEL'));
        $show->field('q-10', __('Quel est votre âge'));
        $show->field('q-11', __('Quel est votre taille'));
        $show->field('q-12', __('Quel est votre poids'));
        $show->field('q-13', __('hypertension artérielle mal équilibrée ? Ou avez-vous une maladie cardiaque ou vasculaire ? Ou prenez vous un traitement à visée cardiologique ?'));
        $show->field('q-14', __('Êtes-vous diabétique ?'));
        $show->field('q-15', __('Avez-vous ou avez-vous eu un cancer ces trois dernières années ?'));
        $show->field('q-16', __('Avez-vous une maladie respiratoire ? Ou êtes-vous suivi par un pneumologue ?'));
        $show->field('q-17', __('Avez-vous une insuffisance rénale chronique dialysée ?'));
        $show->field('q-18', __('Avez-vous une maladie chronique du foie ?'));
        $show->field('q-19', __('Êtes-vous enceinte ?'));
        $show->field('q-20', __('Avez-vous une maladie connue pour diminuer vos défenses immunitaires ?'));
        $show->field('q-21', __('Prenez-vous un traitement immunosuppresseur ? C’est un traitement qui diminue vos défenses contre les infections. Voici quelques exemples : corticoïdes, méthotrexate, ciclosporine, tacrolimus, azathioprine, cyclophosphamide (liste non exhaustive).'));
        $show->field('results_code', __('Results code'));
        $show->field('results_message', __('Results message'));
        $show->field('province', __('Province'));
        $show->field('Ville', __('Ville'));
        $show->field('Commune', __('Commune'));
        $show->field('duration',__('Durée'));
        $show->field('algo_version',__('algo_version'));
        $show->field('created_at', __('Created at'));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Diagnostic());

        $form->number('q-1', __('Q 1'));
        $form->decimal('q-2', __('Q 2'));
        $form->number('q-3', __('Q 3'));
        $form->number('q-4', __('Q 4'));
        $form->number('q-5', __('Q 5'));
        $form->number('q-6', __('Q 6'));
        $form->number('q-7', __('Q 7'));
        $form->number('q-8', __('Q 8'));
        $form->number('q-9', __('Q 9'));
        $form->number('q-10', __('Q 10'));
        $form->number('q-11', __('Q 11'));
        $form->number('q-12', __('Q 12'));
        $form->number('q-13', __('Q 13'));
        $form->number('q-14', __('Q 14'));
        $form->number('q-15', __('Q 15'));
        $form->number('q-16', __('Q 16'));
        $form->number('q-17', __('Q 17'));
        $form->number('q-18', __('Q 18'));
        $form->number('q-19', __('Q 19'));
        $form->number('q-20', __('Q 20'));
        $form->number('q-21', __('Q 21'));
        $form->number('q-22', __('Q 22'));
        $form->number('q-23', __('Q 23'));
        $form->text('q-24', __('Q 24'));
        $form->text('results_code', __('Results code'));
        $form->text('results_message', __('Results message'));
        $form->text('latitude', __('Latitude'));
        $form->text('longitude', __('Longitude'));

        return $form;
    }
}
