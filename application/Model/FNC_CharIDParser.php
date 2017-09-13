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

namespace Model;

use \Doctrine\ORM\Query\AST\Functions\FunctionNode;
use \Doctrine\ORM\Query\Lexer;
use \Doctrine\ORM\Query\SqlWalker;
use \Doctrine\ORM\Query\Parser;

/**
 * Classe para fazer o tratamento para obter
 * o valor do id de personagem em itens.
 */
class FNC_CharIDParser extends FunctionNode
{
    /**
     * Salva informações da primeira carta usada na conta
     * @var int
     */
    private $card3;

    /**
     * Salva informações da segunda carta usada na conta
     * @var int
     */
    private $card4;

    /**
     * Obtém os dados do SQL.
     *
     * @param Doctrine\ORM\Query\SqlWalker $sqlWalker
     * 
     * @return int
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        // Obtém o card3 vindo pelo tratamento.
        $card3 = intval($sqlWalker->walkSimpleArithmeticExpression($this->card3));
        // Obtém o card4 vindo pelo tratamento
        $card4 = intval($sqlWalker->walkSimpleArithmeticExpression($this->card4));

        // Realiza o bitwise para realizar a soma
        // e encontrar o ID do personagem.
        return (($card4<<16)|$card3);
    }

    /**
     * Faz o tratamento da função para retornar as informações.
     *
     * @param Doctrine\ORM\Query\Parser $parser
     */
    public function parse(Parser $parser)
    {
        // Faz o tratamento da função.
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->card3 = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->card4 = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }


}

