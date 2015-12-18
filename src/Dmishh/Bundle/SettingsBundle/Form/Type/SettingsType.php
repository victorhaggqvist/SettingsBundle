<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 *
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\Bundle\SettingsBundle\Form\Type;

use Dmishh\Bundle\SettingsBundle\Exception\SettingsException;
use Dmishh\Bundle\SettingsBundle\Exception\UnknownTypeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Settings management form
 *
 * @author Dmitriy Scherbina <http://dmishh.com>
 * @author Artem Zhuravlov
 */
class SettingsType extends AbstractType
{
    protected $settingsConfiguration;

    public function __construct(array $settingsConfiguration)
    {
        $this->settingsConfiguration = $settingsConfiguration;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->settingsConfiguration as $name => $configuration) {
            // If setting's value exists in data and setting isn't disabled
            if (array_key_exists($name, $options['data']) && !in_array($name, $options['disabled_settings'])) {
                $fieldType = $this->mapType($configuration['validation']['type']);
                $fieldOptions = $configuration['validation']['options'];

                // Validator constraints
                if (!empty($fieldOptions['constraints']) && is_array($fieldOptions['constraints'])) {
                    $constraints = array();
                    foreach ($fieldOptions['constraints'] as $class => $constraintOptions) {
                        if (class_exists($class)) {
                            $constraints[] = new $class($constraintOptions);
                        } else {
                            throw new SettingsException(sprintf('Constraint class "%s" not found', $class));
                        }
                    }

                    $fieldOptions['constraints'] = $constraints;
                }

                // Label I18n
                $fieldOptions['label'] = 'labels.' . $name;
                $fieldOptions['translation_domain'] = 'settings';

                // Choices I18n
                if (!empty($fieldOptions['choices'])) {
                    $fieldOptions['choices'] = array_map(
                        function ($label) use ($fieldOptions) {
                            return $fieldOptions['label'] . '_choices.' . $label;
                        },
                        array_combine($fieldOptions['choices'], $fieldOptions['choices'])
                    );
                }
                $builder->add($name, $fieldType, $fieldOptions);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'disabled_settings' => array(),
            )
        );

        $resolver->addAllowedTypes('disabled_settings', 'array');
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'settings_management';
    }

    /**
     * Map string type to fully qualified class name
     *
     * Switch generated with typemapping.py
     * @param string $type
     * @return string
     */
    private function mapType($type)
    {
        // this allows use of non-core types
        if (class_exists($type)) return $type;

        switch ($type) {
            case 'base': return \Symfony\Component\Form\Extension\Core\Type\BaseType::class;
            case 'birthday': return \Symfony\Component\Form\Extension\Core\Type\BirthdayType::class;
            case 'button': return \Symfony\Component\Form\Extension\Core\Type\ButtonType::class;
            case 'checkbox': return \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class;
            case 'choice': return \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class;
            case 'collection': return \Symfony\Component\Form\Extension\Core\Type\CollectionType::class;
            case 'country': return \Symfony\Component\Form\Extension\Core\Type\CountryType::class;
            case 'currency': return \Symfony\Component\Form\Extension\Core\Type\CurrencyType::class;
            case 'date': return \Symfony\Component\Form\Extension\Core\Type\DateType::class;
            case 'datetime': return \Symfony\Component\Form\Extension\Core\Type\DateTimeType::class;
            case 'email': return \Symfony\Component\Form\Extension\Core\Type\EmailType::class;
            case 'file': return \Symfony\Component\Form\Extension\Core\Type\FileType::class;
            case 'form': return \Symfony\Component\Form\Extension\Core\Type\FormType::class;
            case 'hidden': return \Symfony\Component\Form\Extension\Core\Type\HiddenType::class;
            case 'integer': return \Symfony\Component\Form\Extension\Core\Type\IntegerType::class;
            case 'language': return \Symfony\Component\Form\Extension\Core\Type\LanguageType::class;
            case 'locale': return \Symfony\Component\Form\Extension\Core\Type\LocaleType::class;
            case 'money': return \Symfony\Component\Form\Extension\Core\Type\MoneyType::class;
            case 'number': return \Symfony\Component\Form\Extension\Core\Type\NumberType::class;
            case 'password': return \Symfony\Component\Form\Extension\Core\Type\PasswordType::class;
            case 'percent': return \Symfony\Component\Form\Extension\Core\Type\PercentType::class;
            case 'radio': return \Symfony\Component\Form\Extension\Core\Type\RadioType::class;
            case 'range': return \Symfony\Component\Form\Extension\Core\Type\RangeType::class;
            case 'repeated': return \Symfony\Component\Form\Extension\Core\Type\RepeatedType::class;
            case 'reset': return \Symfony\Component\Form\Extension\Core\Type\ResetType::class;
            case 'search': return \Symfony\Component\Form\Extension\Core\Type\SearchType::class;
            case 'submit': return \Symfony\Component\Form\Extension\Core\Type\SubmitType::class;
            case 'text': return \Symfony\Component\Form\Extension\Core\Type\TextType::class;
            case 'textarea': return \Symfony\Component\Form\Extension\Core\Type\TextareaType::class;
            case 'time': return \Symfony\Component\Form\Extension\Core\Type\TimeType::class;
            case 'timezone': return \Symfony\Component\Form\Extension\Core\Type\TimezoneType::class;
            case 'url': return \Symfony\Component\Form\Extension\Core\Type\UrlType::class;
        }

        throw new UnknownTypeException($type);
    }

}
