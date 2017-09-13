{extends file="firewall.default.tpl"}
{block name="App_Firewall_Content"}


<div ng-controller="login" ng-init="init()">{literal}

    <form class="firewall-form" ng-submit="submit()">

        <div class="firewall-logo"></div>

        <label class="input" data-before="Usuário">
            <input type="text" class="input" ng-model="credentials.username" placeholder="Não é o mesmo de acesso ao sistema." pattern="^[a-zA-ZÀ-ú0-9]{3}[a-zA-ZÀ-ú0-9\s]{1,20}$" maxlength="20" required/>
        </label>
            
        <label class="input" data-before="Senha de acesso" data-after="Deve ter no mínimo 6 caracteres e possuir pelo menos: 1 Letra (inclui espaços), 1 Número e 1 Caractere Especial ($%@)">
            <input type="password" class="input" ng-model="credentials.password" placeholder="Não é o mesmo de acesso ao sistema." required pattern="^((?=.*\d)(?=.*[a-zA-Z\s])(?=.*[@#$%])[a-zA-Z0-9\s@$$%]{6,})$"/>
        </label>

        <div class="form-buttom">
            <button class="button success">Entrar</button>
            <input class="button info" type="reset" value="Limpar"/>
        </div>
    </form>


{/literal}</div>



{/block}
