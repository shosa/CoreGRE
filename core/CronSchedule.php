<?php
/**
 * Cron Schedule Parser
 * Parse e valutazione espressioni cron
 *
 * Supporta:
 *   - Valori singoli: 5
 *   - Range: 1-5
 *   - Liste: 1,3,5
 *   - Step: * /5, 0-30/5
 *   - Wildcard: *
 */

class CronSchedule
{
    /**
     * Verifica se un'espressione cron deve essere eseguita ora
     *
     * @param string $expression Espressione cron (es: "0 2 * * *")
     * @param DateTime $now Data/ora da verificare
     * @return bool
     */
    public static function isDue($expression, ?DateTime $now = null): bool
    {
        if ($now === null) {
            $now = new DateTime();
        }

        // Parsing espressione
        $parts = self::parseExpression($expression);
        if ($parts === false) {
            return false;
        }

        // Estrae componenti data/ora corrente
        $currentMinute = (int) $now->format('i');
        $currentHour = (int) $now->format('G');
        $currentDay = (int) $now->format('j');
        $currentMonth = (int) $now->format('n');
        $currentWeekday = (int) $now->format('N'); // 1=Monday, 7=Sunday

        // Converti weekday cron (0=Sunday, 6=Saturday) a ISO (1=Monday, 7=Sunday)
        $currentWeekdayCron = $currentWeekday === 7 ? 0 : $currentWeekday;

        // Verifica ogni componente
        return self::matchSegment($parts['minute'], $currentMinute, 0, 59)
            && self::matchSegment($parts['hour'], $currentHour, 0, 23)
            && self::matchSegment($parts['day'], $currentDay, 1, 31)
            && self::matchSegment($parts['month'], $currentMonth, 1, 12)
            && self::matchSegment($parts['weekday'], $currentWeekdayCron, 0, 6);
    }

    /**
     * Parse espressione cron
     *
     * @param string $expression
     * @return array|false
     */
    private static function parseExpression($expression)
    {
        $expression = trim($expression);

        // Split per spazi
        $parts = preg_split('/\s+/', $expression);

        if (count($parts) !== 5) {
            error_log("CronSchedule: Espressione cron invalida '{$expression}' - deve avere 5 componenti");
            return false;
        }

        return [
            'minute' => $parts[0],
            'hour' => $parts[1],
            'day' => $parts[2],
            'month' => $parts[3],
            'weekday' => $parts[4]
        ];
    }

    /**
     * Verifica se un segmento dell'espressione cron matcha un valore
     *
     * @param string $segment Segmento espressione (es: "* /5", "1-5", "1,3,5")
     * @param int $value Valore da verificare
     * @param int $min Valore minimo consentito
     * @param int $max Valore massimo consentito
     * @return bool
     */
    private static function matchSegment($segment, $value, $min, $max)
    {
        // Wildcard - match qualsiasi valore
        if ($segment === '*') {
            return true;
        }

        // Step values (es: * /5, 0-30/5)
        if (strpos($segment, '/') !== false) {
            return self::matchStep($segment, $value, $min, $max);
        }

        // Range (es: 1-5)
        if (strpos($segment, '-') !== false) {
            return self::matchRange($segment, $value);
        }

        // Lista (es: 1,3,5,7)
        if (strpos($segment, ',') !== false) {
            return self::matchList($segment, $value);
        }

        // Valore singolo
        return (int) $segment === $value;
    }

    /**
     * Match step values (es: * /5, 0-30/5)
     *
     * @param string $segment
     * @param int $value
     * @param int $min
     * @param int $max
     * @return bool
     */
    private static function matchStep($segment, $value, $min, $max)
    {
        list($range, $step) = explode('/', $segment);
        $step = (int) $step;

        if ($step === 0) {
            return false;
        }

        // Se range è *, usa min-max
        if ($range === '*') {
            $rangeMin = $min;
            $rangeMax = $max;
        } else if (strpos($range, '-') !== false) {
            // Range specifico (es: 0-30/5)
            list($rangeMin, $rangeMax) = explode('-', $range);
            $rangeMin = (int) $rangeMin;
            $rangeMax = (int) $rangeMax;
        } else {
            // Singolo valore con step
            $rangeMin = (int) $range;
            $rangeMax = $max;
        }

        // Verifica se il valore è nel range e multiplo dello step
        if ($value < $rangeMin || $value > $rangeMax) {
            return false;
        }

        return ($value - $rangeMin) % $step === 0;
    }

    /**
     * Match range (es: 1-5)
     *
     * @param string $segment
     * @param int $value
     * @return bool
     */
    private static function matchRange($segment, $value)
    {
        list($start, $end) = explode('-', $segment);
        $start = (int) $start;
        $end = (int) $end;

        return $value >= $start && $value <= $end;
    }

    /**
     * Match lista (es: 1,3,5,7)
     *
     * @param string $segment
     * @param int $value
     * @return bool
     */
    private static function matchList($segment, $value)
    {
        $values = explode(',', $segment);
        $values = array_map('intval', $values);

        return in_array($value, $values);
    }

    /**
     * Descrizione human-readable di un'espressione cron
     *
     * @param string $expression
     * @return string
     */
    public static function describe($expression)
    {
        $parts = self::parseExpression($expression);
        if ($parts === false) {
            return "Espressione invalida";
        }

        // Casi comuni
        if ($expression === '* * * * *') {
            return "Ogni minuto";
        }

        if ($expression === '0 * * * *') {
            return "Ogni ora";
        }

        if ($expression === '0 0 * * *') {
            return "Ogni giorno a mezzanotte";
        }

        if ($expression === '0 0 * * 0') {
            return "Ogni domenica a mezzanotte";
        }

        if ($expression === '0 0 1 * *') {
            return "Il primo giorno di ogni mese";
        }

        if (preg_match('/^\*\/(\d+) \* \* \* \*$/', $expression, $matches)) {
            return "Ogni {$matches[1]} minuti";
        }

        if (preg_match('/^0 \*\/(\d+) \* \* \*$/', $expression, $matches)) {
            return "Ogni {$matches[1]} ore";
        }

        if (preg_match('/^0 (\d+) \* \* \*$/', $expression, $matches)) {
            return "Ogni giorno alle {$matches[1]}:00";
        }

        // Descrizione generica
        $desc = [];

        // Minuti
        if ($parts['minute'] !== '*') {
            $desc[] = "minuto: " . $parts['minute'];
        }

        // Ore
        if ($parts['hour'] !== '*') {
            $desc[] = "ora: " . $parts['hour'];
        }

        // Giorno
        if ($parts['day'] !== '*') {
            $desc[] = "giorno: " . $parts['day'];
        }

        // Mese
        if ($parts['month'] !== '*') {
            $monthNames = [
                '',
                'Gennaio',
                'Febbraio',
                'Marzo',
                'Aprile',
                'Maggio',
                'Giugno',
                'Luglio',
                'Agosto',
                'Settembre',
                'Ottobre',
                'Novembre',
                'Dicembre'
            ];
            $monthNum = (int) $parts['month'];
            $desc[] = "mese: " . ($monthNames[$monthNum] ?? $parts['month']);
        }

        // Weekday
        if ($parts['weekday'] !== '*') {
            $weekdayNames = ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'];
            $weekdayNum = (int) $parts['weekday'];
            $desc[] = "giorno settimana: " . ($weekdayNames[$weekdayNum] ?? $parts['weekday']);
        }

        return !empty($desc) ? implode(', ', $desc) : "Schedule personalizzato";
    }

    /**
     * Calcola prossima esecuzione
     *
     * @param string $expression
     * @param DateTime $from Data di partenza
     * @param int $maxIterations Limite iterazioni per prevenire loop infiniti
     * @return DateTime|null
     */
    public static function nextRunDate($expression, ?DateTime $from = null, int $maxIterations = 10000): ?DateTime
    {
        if ($from === null) {
            $from = new DateTime();
        }

        $current = clone $from;
        $current->modify('+1 minute');
        $current->setTime((int) $current->format('H'), (int) $current->format('i'), 0);

        $iterations = 0;

        while ($iterations < $maxIterations) {
            if (self::isDue($expression, $current)) {
                return $current;
            }

            $current->modify('+1 minute');
            $iterations++;
        }

        return null; // Non trovato entro limite iterazioni
    }
}
