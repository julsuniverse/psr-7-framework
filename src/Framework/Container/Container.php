<?php

namespace Framework\Container;

class Container implements ContainerInterface
{
    /** @var array
     * Хранит список сервисов
     */
    private $definitions;

    /** @var array
     * Кеширует экземпляры сервисов
     */
    private $results = [];

    public function __construct(array $definitions = [])
    {
        $this->definitions = $definitions;
    }

    public function get($id)
    {
        /** Достаем из кеша */
        if (array_key_exists($id, $this->results)) {
            return $this->results[$id];
        }

        /**
         * MidlewareResolver пытается достать класс (сервис) из контейнера.
         * Если такого сервиса в контейнере нету, контейнер просто создаст
         * класс через new и вернет его.
         * Если у класса есть какой-то конструктор, то PHP вылетит с ошибкой,
         * что не может создать класс.
         * Если такого класса нету, выкинет исключение.
         */
        if (!array_key_exists($id, $this->definitions)) {
            if(class_exists($id)) {

                /** Рефлексией создаем класс */
                $reflection = new \ReflectionClass($id);

                $arguments = [];
                /**
                 * Из созданного рефлексией класса берем конструктор,
                 * у конструктора перебираем параметры,
                 * у параметра достаем класс,
                 * берем его название и записываем в массив $arguments
                 */
                if(($constructor = $reflection->getConstructor()) !== null) {
                    foreach ($constructor->getParameters() as $param) {
                        /**
                         * Если параметр - класс, достаем его,
                         * берем его название и записываем в массив $arguments
                         */
                        if ($paramClass = $param->getClass()) {
                            $arguments[] = $this->get($paramClass->getName());
                        } elseif ($param->isArray()) {
                            /** Если параметр - массив,
                             *  то записывает в $arguments пустой массив
                             */
                            $arguments = [];
                        } else {
                            /** Если у аргумента есть значение по умолчанию,
                             * то оно записывается в $arguments
                             */
                            if(!$param->isDefaultValueAvailable()) {
                                throw new ServiceNotFoundException('Unable to resolve "' . $param->getName() . '"" in service "' . $id . '"');
                            }
                            $arguments[] = $param->getDefaultValue();
                        }
                    }
                }

                /** Cоздаем рефлексией классы из параметров конструктора */
                $this->results[$id] = $reflection->newInstanceArgs($arguments);

                return $this->results[$id];
            }
            throw new ServiceNotFoundException('Unknown service "' . $id . '""');
        }

        $definition =  $this->definitions[$id];

        /**
         * Если $definition объект класса Closure (является анонимной функции), то
         * мы вызываем на исполнение эту анонимную функцию и результат записываем в массив results.
         * Если нет, записываем в results содержимое вызванного сервиса.
         *
         * Анонимная функция используется для того, чтобы
         * не создавать экземпляр объекта при записи в контейнер,
         * а создавать только при вызове нужного сервиса.
         *
         * Записываем в кеш (в $results), чтобы не создавать повторно экземпляр.
         */
        if($definition instanceof \Closure) {
            $this->results[$id] = $definition($this);
        } else {
            $this->results[$id] = $definition;
        }

        return $this->results[$id];

    }

    public function set($id, $value): void
    {
        /** Удаляем из кеша, если такой элемент уже есть */
        if (array_key_exists($id, $this->results)) {
            unset($this->results[$id]);
        }
        $this->definitions[$id] = $value;
    }

    /**
     * Проверяет, что для такого id в нашем контейнере есть определение
     * ИЛИ существует такой класс
     */
    public function has($id): bool
    {
        return array_key_exists($id, $this->definitions) || class_exists($id);
    }
}