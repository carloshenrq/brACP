<?php

/**
 * Classe para o formatador de campos.
 */
class AppFormatter extends AppComponent
{
    /**
     * Converte bytes para string para algo mais legivel.
     *
     * @param int $bytes
     * @param int $byteLimit
     *
     * @return string Bytes convertidos.
     */
    public function bytes2str($bytes, $byteLimit = 1024)
    {
        // Formatos aceitos
        $format = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $count = count($format);
        $totalBytes = $bytes;

        // Não usar bytes negativos...
        $bytes = max(0, $bytes);

        // Indice do vetor.
        $index = min($count - 1, floor(log($bytes) / log($byteLimit)));

        // Bytes finais para retorno em tela.
        if($bytes > 0)
            $bytes /= pow($byteLimit, $index);

        return sprintf('%.2f %s (%s bytes)', $bytes, $format[$index], $totalBytes);
    }

    /**
     * Obtém o tipo de denuncia realizada.
     *
     * @param string $type
     *
     * @return string Tipo de denuncia.
     */
    public function reportType($type)
    {
        switch($type)
        {
            case "F": return "Ofensivo/Agressivo";
            case "S": return "Conteúdo sexual/Pornografia";
            case "B": return "Bullyng/Difamação";
            case "O": return "Outros";
            default: return "Desconhecido";
        }
    }
    // -- 'F' = Ofensivo
    // -- 'S' = Conteúdo sexual
    // -- 'B' = Bullying
    // -- 'O' = Outros

    /**
     * Retorna o gênero para o perfil.
     *
     * @param string $gender
     */
    public function gender($gender)
    {
        if($gender == 'M')
            return 'Masculino';
        else if($gender == 'F')
            return 'Feminino';
        else
            return 'Outros';
    }

    /**
     * Formata uma data para algo mais legivel para as pessoas.
     *
     * @param string $date2format Data a ser formatada.
     * @param bool $full ignora as validações e retorna a data completa.
     * @param string $hour Formato para hora.
     */
    public function date($date2format, $full = false, $hour = '%H:%M')
    {
        $recvDate = new DateTime($date2format);
        $currDate = new DateTime('now');
        $diffDate       = $currDate->diff($recvDate);

        $timestamp      = $recvDate->format('U');

        $semanaNome     = ucfirst(strftime('%A', $timestamp));
        $diaMes         = strftime('%d', $timestamp);
        $mesNome        = htmlspecialchars(utf8_encode(ucfirst(strftime('%B', $timestamp))));
        $ano            = strftime('%Y', $timestamp);

        $hora = '';
        if($hour !== false)
            $hora = ' &agrave;s ' . strftime($hour, $timestamp);

        if($full || $diffDate->days >= 365)
            return sprintf('%s, %d de %s de %d%s', $semanaNome, $diaMes, $mesNome, $ano, $hora);

        $strdate = (($diffDate->invert) ? 'H&aacute; ':'Daqui ');

        if($diffDate->m > 0)
            $strdate = sprintf('%s%d %s. %s, %d de %s %s', $strdate,
                        $diffDate->m, (($diffDate->m > 1) ? 'meses' : 'm&ecirc;s'),
                        $semanaNome, $diaMes, $mesNome, $hora);
        else if($diffDate->d > 0)
        {
            $strdate = sprintf('%s%d dia%s', $strdate, $diffDate->d,
                (($diffDate->d > 1) ? 's':'') );

            if($diffDate->h > 0 && !$diffDate->invert)
                $strdate = sprintf('%s e %d hora%s', $strdate, $diffDate->h, (($diffDate->h > 1) ? 's':''));
            else if($diffDate->i > 0 && !$diffDate->invert)
                $strdate = sprintf('%s e %d minuto%s', $strdate, $diffDate->i, (($diffDate->i > 1) ? 's':''));
        }
        else if($diffDate->h > 0)
        {
            $strdate = sprintf('%s%d hora%s', $strdate, $diffDate->h, (($diffDate->h > 1) ? 's':''));

            if($diffDate->h < 6 && !$diffDate->invert)
                $strdate = sprintf('%s e %d minuto%s', $strdate, $diffDate->i, (($diffDate->i > 1) ? 's':''));
        }
        else if($diffDate->i > 0)
            $strdate = sprintf('%s%d minuto%s', $strdate, $diffDate->i, (($diffDate->i > 1) ? 's':''));
        else if($diffDate->s > 30)
            $strdate = sprintf('%s menos de um minuto', $strdate);
        else
            $strdate = sprintf('Agora', $strdate);

        return $strdate;
    }
}
