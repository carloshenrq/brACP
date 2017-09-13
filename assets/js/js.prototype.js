
/**
 * Busca no array atual de acordo com valor e callback de comparação.
 * 
 * @param callback func
 */
Array.prototype.where = function(func)
{
    var _tmp = [];
    this.forEach(function(value, index) {
        if(func(value, index) == true)
            _tmp.push(value);
    });
    return _tmp;
};
