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
 * Classe para controlar os dados de template para o brACP.
 *
 * @author CarlosHenrq
 */
class brACPSmarty extends Smarty
{
    use \TMod;

    /**
     * Construtor para os dados de template do brACP.
     */
    public function __construct()
    {
        // Chama o construtor do smarty.
        parent::__construct();

        $this->setTemplateDir(BRACP_TEMPLATE_DIR);

        // Em modo desenvolvedor, desliga a geração de cache.
        if(BRACP_DEVELOP_MODE)
            $this->setCaching(Smarty::CACHING_OFF);

        // Registra o plugin para uso do smarty.
        $this->registerPlugin('block', 'translate', [
            brACPApp::getInstance()->getLanguage(),
            '__translate'
        ], false, null);

        // Carrega os módulos para aplicação de alteração do objeto atual.
        $this->loadMods(null, 0);
    }
}
