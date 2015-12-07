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

// Varre todos os elementos do array.
Array.prototype.each = function(fncCallBack)
{
    for(var i = 0; i < this.length; i++)
        fncCallBack(this[i]);
};

// Procura no array as informações para o callback informado.
Array.prototype.where = function(fncCallBack)
{
    var tmpArray = [];

    for(var i = 0; i < this.length; i++)
    {
        if(fncCallBack(i, this[i]) == true)
            tmpArray.push(this[i]);
    }

    return tmpArray;
};

// Aplica o where e retorna o primeiro elemento do array.
Array.prototype.first = function(fncCallBack)
{
    return this.where(fncCallBack).shift() || null;
};

// Obtém o ultimo elemento do elemento.
Array.prototype.last = function(fncCallBack)
{
    return this.where(fncCallBack).pop() || null;
};

