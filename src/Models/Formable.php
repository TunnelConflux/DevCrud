<?php
/**
 * Project      : DevCrud
 * File Name    : Form.php
 * Author       : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2019/07/09 12:17 PM
 */

namespace TunnelConflux\DevCrud\Models;

class Formable
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $value;
    /**
     * @var string
     */
    public $class;
    /**
     * @var bool
     */
    public $disable;
    /**
     * @var bool
     */
    public $multiple;
    /**
     * @var string
     */
    public $placeholder;
    /**
     * @var array
     */
    public $options;
    /**
     * @var bool
     */
    public $editor;

    public $attribute;

    /**
     * Form constructor.
     *
     * @param $id
     * @param $name
     * @param $type
     * @param $title
     * @param $value
     * @param $class
     * @param $disable
     * @param $multiple
     * @param $placeholder
     * @param $editor
     */
    public function __construct(
        $title,
        $name,
        $type,
        $value = "",
        $placeholder = null,
        $class = "",
        $id = null,
        $disable = false,
        $multiple = false,
        $editor = false
    )
    {
        $this->id          = $id;
        $this->name        = $name;
        $this->type        = $type;
        $this->title       = $title;
        $this->value       = $value?:"";
        $this->class       = $class;
        $this->disable     = $disable;
        $this->multiple    = $multiple;
        $this->placeholder = $placeholder;
        $this->editor      = $editor;
    }

    /**
     * @param string $id
     *
     * @return self
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string $title
     *
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return self
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param string $class
     *
     * @return self
     */
    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @param bool $disable
     *
     * @return self
     */
    public function setDisable(bool $disable): self
    {
        $this->disable = $disable;

        return $this;
    }

    /**
     * @param bool $multiple
     *
     * @return self
     */
    public function setMultiple(bool $multiple): self
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * @param string $placeholder
     *
     * @return self
     */
    public function setPlaceholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @param array $options
     *
     * @return self
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param bool $editor
     *
     * @return self
     */
    public function setEditor(bool $editor): self
    {
        $this->editor = $editor;

        return $this;
    }


    /*public static function setTextField()
    {
        (new static)->__construct()
    }*/
}