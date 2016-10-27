<?php
/**
 *  brACP - brAthena Control Panel for Ragnarok Emulators
 *  Copyright (C) 2015  brAthena, CHLFZ
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Classe para formatação dos dados.
 *
 * @static
 */
class Format
{
    public static function bytes($bytes)
    {
        $format = ['B', 'KB', 'MB', 'GB'];

        $bytes = $totalBytes = max(0, $bytes);
        $pow = floor(log($bytes)/log(1024));

        $bytes /= pow(1024, $pow);

        return sprintf('%.2f %s (%d bytes)', $bytes, $format[$pow], $totalBytes);
    }

    /**
     * Formata um item para seus dados e informações.
     */
    public static function item($id, $amount = 0, $refine = 0, $broken = false)
    {
        $item = Controller\Item::get($id);

        return '<div class="item-info '.(($broken) ? 'item-broken':'').'" '.(($refine > 0) ? 'data-refine="'.$refine.'"':'').' '.(($amount > 1) ? 'data-amount="'.$amount.'"':'').' '.(($item->slots > 0) ? 'data-slot="'.$item->slots.'"':'').' style="background-image: url('.$item->icon.');">'.
                    $item->name . 
               '</div>';
    }

    /**
     * Formata informações de data do formato indicado até o destino.
     *
     * @param string $date
     * @param string $destFormat
     * @param string $fromFormat
     *
     * @return string
     */
    public static function date($date, $destFormat = 'd/m/Y H:i:s')
    {
        return date($destFormat, strtotime($date));
    }

    /**
     * Calcula a diferença de datas e retorna em formato de string.
     *
     * @param string $start
     * @param string $end
     *
     * @return string
     */
    public static function date_diff($start, $end, $format = 'Y-m-d H:i:s')
    {
        $str = '';
        $interval = date_diff(date_create_from_format($format, $end),
                        date_create_from_format($format, $start), true);

        $tmp = [];

        if($interval->y > 0) $tmp[] = $interval->y . ' ano'.(($interval->y > 1) ? 's':'');
        if($interval->m > 0) $tmp[] = $interval->m . ' '.(($interval->m > 1) ? 'meses':'mês');
        if($interval->d > 0) $tmp[] = $interval->d . ' dia' . (($interval->d > 1) ? 's':'');
        if($interval->h > 0) $tmp[] = $interval->h . ' hora' . (($interval->h > 1) ? 's':'');
        if($interval->i > 0) $tmp[] = $interval->i . ' minuto' . (($interval->i > 1) ? 's':'');
        if($interval->s > 0) $tmp[] = $interval->s . ' segundo' . (($interval->s > 1) ? 's':'');

        for($i = 0; $i < count($tmp); $i++)
        {
            $str .= $tmp[$i];

            if(($i + 1) < (count($tmp) - 1))
                $str .= ', ';
            else if(($i + 1) == (count($tmp) - 1))
                $str .= ' e ';

        }
        return $str;
    }

    /**
     * Protege o endereço de e-mail exibindo apenas o primeiro e ultimo caractere antes do @
     *
     * @param string $email
     *
     * @return string
     */
    public static function protectMail($email)
    {
        preg_match('/^([a-z0-9._%+-])([^\@]+)([a-z0-9._%+-])(.*)/i', $email, $match);

        array_shift($match);
        $match[1] = preg_replace('([a-z0-9._%+-])', '*', $match[1]);

        return implode('', $match);
    }

    /**
     * Formata o falor enviado como dinheiro.
     */
    public static function money($money, $decimalPlaces = 2, $centsDelimiter = ',', $milharDelimiter = '.')
    {
        $_pow = pow(10, $decimalPlaces);
        $_money = floor(floatval($money) * $_pow);
        $_cents = substr(str_pad(strval($_money), ($decimalPlaces + 1), '0', STR_PAD_RIGHT), $decimalPlaces * -1);
        $money = floor($_money/$_pow);

        return self::zeny($money, $milharDelimiter) . $centsDelimiter . $_cents;
    }

    /**
     * Obtém o status do jogador.
     *
     * @param int $online
     *
     * @return string
     */
    public static function status($online)
    {
        return sprintf(self::$online[$online], brACPApp::getInstance()->getLanguage()->getTranslate('@STATUS_'.$online.'@'));
    }

    /**
     * Obtém o nome da classe para o personagem.
     *
     * @param int $job_class Código da classe.
     *
     * @return string
     */
    public static function job($job_class)
    {
        return brACPApp::getInstance()->getLanguage()->getTranslate('@JOBS_' .$job_class.'@');
    }

    /**
     * Formata os zenys.
     *
     * @param int $zeny
     *
     * @return string
     */
    public static function zeny($zeny, $delimiter = '.')
    {
        return strrev(implode($delimiter, str_split(strrev($zeny), 3)));
    }

    /**
     * Define o status do jogador com o texto formatado.
     *
     * @var array
     */
    private static $online = [
        0 => '<span style="color: red;">%s</span>',
        1 => '<span style="color: green;">%s</span>',
    ];
}
