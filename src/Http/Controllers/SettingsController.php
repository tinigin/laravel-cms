<?php

namespace LaravelCms\Http\Controllers;

use LaravelCms\Facades\Toast;
use LaravelCms\Form\Actions\Button;
use LaravelCms\Form\Builder;
use LaravelCms\Form\Fields\Input;
use LaravelCms\Form\Repository;
use LaravelCms\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SettingsController extends BaseController
{
    public function before()
    {
        if (parent::before()) {
            if (
                $this->getSection()->is_published != true ||
                !$this->getSection()->users()->where('id', Auth::id())->count()
            ) {
                throw new NotFoundHttpException;
            }

            return true;
        }
    }
    /**
     * Array of validation rules
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|max:255|email',
        ];
    }

    /**
     * Return array of form fields
     * @return array
     */
    protected function getFormFields(): array
    {
        return [
            Input::make('email')
                ->title('Адрес эл. почты')
                ->type('email')
                ->required()
                ->horizontal(),
        ];
    }

    public function index()
    {
        $formFields = $this->getFormFields();
        $values = config('settings', []);
        $repository = new Repository($values);

        $form = new Builder($formFields, $repository);

        $form->push(
            Button::make('save')
                ->label('Сохранить')
                ->value('true')
                ->class('btn btn-primary')
        );
        $form->setAction(route('cms.module.store', ['controller' => $this->getSectionController()]));

        $form->view('card');

        return view('cms::module')
            ->with('form', $form)
            ->with('title', 'Настройки');
    }

    public function store()
    {
        $validated = $this->validate(
            request(),
            $this->rules()
        );

        // default value for boolean fields
        foreach ($this->rules() as $key => $validators) {
            if (
                (is_string($validators) && strpos($validators, 'boolean') !== false) ||
                (is_array($validators) && in_array('boolean', $validators))
            ) {
                if (!isset($validated[$key])) {
                    $validated[$key] = false;
                }
            }
        }

        File::put(config_path('settings.php'), "<?php\nreturn " . var_export($validated, true) . ";");

        Toast::success('Данные успешно сохранены');

        redirect()->to(route(
            'cms.module.index',
            ['controller' => $this->getSectionController()]
        ))->send();
    }
}
