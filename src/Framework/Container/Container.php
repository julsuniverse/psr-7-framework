<?php

namespace Framework\Container;

class Container
{
    /** @var array
     * Хранит список сервисов
     */
    private $definitions = [];

    /** @var array
     * Кеширует экземпляры сервисов
     */
    private $results = []; //сюда кешируем

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
                return $this->results[$id] = new $id();
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
}