<?php


namespace app\framework\cron;

abstract class AbstractJob
{
    /**
     * время текущей записи задания
     *  * * * * *  выполняемая команда
     *  - - - - -
     *  | | | | |
     *  | | | | ----- день недели (0—7) (воскресенье = 0 или 7)
     *  | | | ------- месяц (1—12)
     *  | | --------- день месяца (1—31)
     *  | ----------- час (0—23)
     *  ------------- минута (0—59)
     *
     * @var string
     */
    protected static $schedule = '* * * * *';

    public static function isEnable()
    {
        return static::isTimeMatchingCron(static::$schedule);
    }

    /**
     *  проверяет доступна ли текущая задача в данный момент времени
     *
     * @param $cronString
     * @return bool
     */
    protected static function isTimeMatchingCron($cronString) {
        // Разбиваем маску времени на компоненты
        $cronParts = preg_split('/\s+/', $cronString, -1, PREG_SPLIT_NO_EMPTY);

        // Проверяем, что маска времени состоит из 5 частей
        if (count($cronParts) !== 5) {
            return false;
        }

        // Получаем текущее время и дату
        $currentTime = time();
        $currentDate = getdate($currentTime);

        // Получаем текущий день, месяц и год
        $currentDay = $currentDate['mday'];
        $currentMonth = $currentDate['mon'];
        $currentYear = $currentDate['year'];

        // Разбиваем маску времени на компоненты
        list($minute, $hour, $dayOfMonth, $month, $dayOfWeek) = $cronParts;

        // Проверяем совпадение минут и часов
        if (!static::isPartMatching($minute, $currentDate['minutes'])) {
            return false;
        }
        if (!static::isPartMatching($hour, $currentDate['hours'])) {
            return false;
        }

        // Проверяем совпадение дня месяца
        if (!static::isPartMatching($dayOfMonth, $currentDay)) {
            return false;
        }

        // Проверяем совпадение месяца
        if (!static::isPartMatching($month, $currentMonth)) {
            return false;
        }

        // Проверяем совпадение дня недели
        if (!static::isPartMatching($dayOfWeek, $currentDate['wday'])) {
            return false;
        }

        // Если все проверки пройдены, то текущее время подходит под маску времени
        return true;
    }

    /**
     * Вспомогательная функция для проверки соответствия компонента маски
     */
    protected static function isPartMatching($part, $currentValue) {
        $subParts = explode(',', $part);
        foreach ($subParts as $subPart) {
            if (strpos($subPart, '/') !== false) {
                list($step, $divider) = explode('/', $subPart);
                if (($currentValue % $divider == 0) && static::isPartMatching($step, $currentValue)) {
                    return true;
                }
            } elseif (strpos($subPart, '-') !== false) {
                list($start, $end) = explode('-', $subPart);
                if ($currentValue >= $start && $currentValue <= $end) {
                    return true;
                }
            } elseif ($subPart === '*' || $subPart == $currentValue) {
                return true;
            }
        }
        return false;
    }

    /**
     * cron execute function.
     *
     * @return bool
     */
    public abstract function execute();
}