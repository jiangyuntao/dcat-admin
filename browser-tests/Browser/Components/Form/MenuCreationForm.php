<?php

namespace Tests\Browser\Components\Form;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\Component;
use Tests\Browser\Components\Form\Field\MultipleSelect2;
use Tests\Browser\Components\Form\Field\Select2;
use Tests\Browser\Components\Form\Field\Tree;

class MenuCreationForm extends Component
{
    protected $selector;

    public function __construct($selector = 'form[method="POST"]')
    {
        $this->selector = $selector;
    }

    /**
     * 获取组件的 css selector
     *
     * @return string
     */
    public function selector()
    {
        return $this->selector;
    }

    /**
     * 浏览器包含组件的断言
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertSee(__('admin.submit'))
            ->assertSee(__('admin.reset'))
            ->within('@form', function (Browser $browser) {
                $browser
                    ->assertSee(__('admin.parent_id'))
                    ->assertSee(__('admin.title'))
                    ->assertSee(__('admin.icon'))
                    ->assertSee(__('admin.uri'))
                    ->assertSee(__('admin.roles'))
                    ->assertSee(__('admin.permission'))
                    ->assertSee(__('admin.selectall'))
                    ->assertSee(__('admin.expand'))
                    ->hasInput('title')
                    ->hasInput('icon')
                    ->hasInput('uri')
                    ->assertSelected('parent_id', 0)
                    ->assert(new Tree('permissions'))
                    ->assert(new Select2('select[name="parent_id"]'))
                    ->assert(new MultipleSelect2('select[name="roles[]"]'));
            });
    }

    /**
     * 注入表单
     *
     * @param Browser $browser
     * @param array $input
     *
     * @return Browser
     */
    public function fill(Browser $browser, array $input)
    {
        $inputKeys = [
            'title',
            'icon',
            'uri',
        ];

        $selectKeys = [
            'parent_id'
        ];

        $multipleSelectKeys = [
            'roles',
        ];

        foreach ($input as $key => $value) {
            if (in_array($key, $inputKeys, true)) {
                $browser->type($key, $value);

                continue;
            }

            if (in_array($key, $selectKeys, true)) {
                $selector = sprintf('select[name="%s"]', $key);
                $browser->within(new Select2($selector), function ($browser) use ($value) {
                    $browser->choose($value);
                });

                continue;
            }

            if (in_array($key, $multipleSelectKeys, true)) {
                $selector = sprintf('select[name="%s[]"]', $key);
                $browser->within(new MultipleSelect2($selector), function ($browser) use ($value) {
                    $browser->choose($value);
                });

                continue;
            }

            if ($key === 'permissions') {
                $browser->within(new Tree($key), function ($browser) use ($value) {
                    $browser->choose($value);
                });
            }
        }

        return $browser;
    }

    /**
     * 读取组件的元素快捷方式
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@form' => $this->selector,
        ];
    }
}
