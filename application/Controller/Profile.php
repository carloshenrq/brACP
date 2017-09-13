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

namespace Controller;

/**
 * Controlador para os dados de profile.
 */
class Profile extends AppController
{
    /**
     * Obtém os dados do usuário logado no sistema.
     * @var \Model\Profile
     */
    private static $loggedUser;

    /**
     * @see AppController::init()
     */
    protected function init()
    {
        // Define o repositorio de dados com informações de profile 
        // Para realizar o login.
        $this->setRepository($this->getApp()->getEntityManager()->getRepository('Model\Profile'));

        // Obtém todas as rotas para o profile.
        $allRoutes = $this->getAllRoutes();

        // Rotas que não podem estar logadas para usar.
        $notLoggedIn = ['login_POST', 'create_POST'];

        // Aplica em todas as rotas as restrições.
        foreach($this->getAllRoutes() as $route)
        {
            if(in_array($route, $notLoggedIn))
                $this->addRouteRestriction($route, function() {
                    return !Profile::isLoggedIn();
                });
            else
                $this->addRouteRestriction($route, function() {
                    return Profile::isLoggedIn();
                });
        }

        // Trava as rotas de criação de contas caso a mesma esteja desabilitada.
        if(!BRACP_ACCOUNT_CREATE)
            $this->addRouteRestriction('create_POST', function() {
                return false;
            });
        
        // Trava as rotas de verificação de contas caso a mesma não esteja habilitada.
        if(!BRACP_ACCOUNT_VERIFY)
        {
            $this->addRouteRestriction('verify_GET', function() {
                return false;
            });

            $this->addRouteRestriction('verify_POST', function() {
                return false;
            });

            $this->addRouteRestriction('verify_resend_POST', function() {
                return false;
            });
        }
    }

    public function test_GET($response, $args)
    {
        try
        {
            $asset = new Asset($this->getApp());
            $em = $this->getApp()->getEntityManager();
            $css = $asset->getCssFile('app.mail.scss');
            $css .= ' ' . $asset->getCssFile('app.message.scss');

            $profile = $this->getRepository()->findById(1);

            return $this->render($response, 'mail.profile.notify.blocked.tpl', [
                'profile'       => $profile,
                'blocked'       => true,
                'css'           => $css,
                'subject'       => 'Teste',
                'urlSender'     => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
            ]);
        }
        catch(\Exception $ex)
        {
            echo $ex->getMessage();
        }
    }

    /**
     * Grava a denuncia do perfil informado.
     *
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function report_POST($response, $args)
    {
        return $response->withJson([
            'success'       => $this->getRepository()
                                    ->reportProfile(
                                        self::getLoggedUser(),
                                        $this->post['profileId'],
                                        $this->post['reasonType'],
                                        $this->post['reasonText']
                                    )
        ]);
    }

    /**
     * Rota para realizar bloqueios entre perfils.
     *
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function block_POST($response, $args)
    {
        return $response->withJson([
            'success'   => $this->getRepository()->blockProfileView(self::getLoggedUser(), $this->post['profileId'])
        ]);
    }

    /**
     * Rota para realizar bloqueios entre perfils.
     *
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function unblock_POST($response, $args)
    {
        return $response->withJson([
            'success'   => $this->getRepository()->unblockProfileView(self::getLoggedUser(), $this->post['profileId'])
        ]);
    }

    /**
     * Rota para exibir um perfil em busca.
     *
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function view_GET($response, $args)
    {
        // Caso não tenha enviado o código do perfil
        // Para visualização.
        if(!isset($args->id))
            throw new AppControllerNotFoundException($this);

        // Dados de perfil para ser visualizado.
        $id = $args->id;
        $profile = null;
        $selfProfile = $allowedSee = $blocked = $admin = false;
        $viewerVisibility = 'P';

        // Se for um usuário logado e este está tentando visualizar o próprio perfil...
        // Então define o profile como ele mesmo.
        // * Administradores podem visualizar tudo...
        if(self::isLoggedIn() && ($id == self::getLoggedUser()->id
            || self::getLoggedUser()->privileges == 'A'))
        {
            $selfProfile = ($id == self::getLoggedUser()->id);
            $viewerVisibility = 'M';
            $profile = self::getLoggedUser();
            $admin = (!$selfProfile && self::getLoggedUser()->privileges == 'A');
        }
        else
            $profile = $this->getRepository()->findById($id);

        // Se não existir dados do perfil, então retorna como página de erro.
        if(is_null($profile))
            throw new AppControllerNotFoundException($this);

        // Verifica usuário logado para testar se o mesmo foi bloqueado
        if(self::isLoggedIn() && !$admin)
        {
            // Verifica se quem está tentando visualizar não está bloqueado.
            $blocked = $this->getRepository()->isBlockedProfile(self::getLoggedUser(), $profile);
        }

        // Se os dados de acesso forem públicos, então é possível visualizar
        // Todas as informações deste perfil
        if($profile->visibility == 'P' && !$blocked)
        {
            $allowedSee = true;

            // Verifica usuário logado para testar se o mesmo foi bloqueado
            if(self::isLoggedIn() && !$admin)
            {
                // Verifica se o outro usuário, também não fez o bloqueio do usuário logado
                $allowedSee = !$this->getRepository()->isBlockedProfile($profile, self::getLoggedUser());
            }
        }
        else
        {
            // @Todo: Verificações para saber se é possível visualizar o perfil
            //        do infeliz
        }

        // Devolve os dados de visualização em tela para o perfil indicado.
        return $this->render($response, 'bracp.profile.view.tpl', [
            'selfProfile'       => $selfProfile,
            'allowedSee'        => $allowedSee,
            'blocked'           => $blocked,
            'profile'           => $profile,
            'admin'             => $admin,
            'viewerVisibility'  => $viewerVisibility
        ]);
    }

    /**
     * Obtém os dados do perfil atual.
     *
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function me_GET($response, $args)
    {
        return $this->view_GET($response, (object)['id' => self::getLoggedUser()->id]);
    }

    /**
     * Obtém os dados de chamados realizados.
     *
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function support_GET($response, $args)
    {
        return $this->render($response, 'bracp.profile.support.tpl');
    }

    /**
     * Caminho para salvar os dados de perfil.
     */
    public function config_POST($response, $args)
    {
        try
        {
            // Obtém o perfil do usuário logado.
            $profile = self::getLoggedUser();

            // Verifica o tamanho máximo para o avatar enviado
            // No caso de alguém burlar a segurança...
            if(preg_match('/^data\:(?:[^\,]+),(.*)$/i', $this->post['avatarUrl'], $matches))
            {
                $avatarLength = strlen(base64_decode($matches[1]));

                // Problema ao salvar arquivo...
                if($avatarLength > 204800)
                    throw new AppControllerException('Arquivo muito grande para ser armazenado.');
            }

            // Atualiza o perfil com os dados da tela.
            $profile->aboutMe           = $this->post['aboutMe'];
            $profile->allowMessage      = $this->post['allowMessage'];
            $profile->avatarUrl         = $this->post['avatarUrl'];
            $profile->birthdate         = new \DateTime($this->post['birthdate']);
            $profile->gender            = $this->post['gender'];
            $profile->name              = $this->post['name'];
            $profile->showBirthdate     = $this->post['showBirthdate'];
            $profile->showEmail         = $this->post['showEmail'];
            $profile->showFacebook      = $this->post['showFacebook'];
            $profile->visibility        = $this->post['visibility'];

            // Zera informações do perfil se for utilizado
            // A removido a imagem de perfil.
            if($profile->avatarUrl == APP_URL_PATH . '/asset/img/default.png')
                $profile->avatarUrl = null;

            // Grava no banco de dados a atualização e gera log para a operação.
            $this->getRepository()->update($profile);
            $this->getRepository()->addLog($profile, 'O', 'Atualizou informações do perfil.');

            // Devolve resposta que foi tudo salvo e atualizado com sucesso.
            return $response->withJson([
                'success'   => true
            ]);
        }
        catch(\Exception $ex)
        {
            return $response->withJson([
                'error'   => true,
                'message' => $ex->getMessage(),
            ]);
        }
    }

    /**
     * Obtém informações de configuração do perfil.
     *
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function config_GET($response, $args)
    {
        $avatarUrl = APP_URL_PATH . '/asset/img/default.png';
        if(!empty(self::getLoggedUser()->avatarUrl))
            $avatarUrl = self::getLoggedUser()->avatarUrl;
        
        $aboutMe = '';
        if(!empty(self::getLoggedUser()->aboutMe))
            $aboutMe = self::getLoggedUser()->aboutMe;

        return $this->render($response, 'bracp.profile.config.tpl', [
            'blankAvatar'   => APP_URL_PATH . '/asset/img/default.png',
            'profile'   => base64_encode(json_encode((object)[
                'name'          => self::getLoggedUser()->name,
                'birthdate'     => self::getLoggedUser()->birthdate->format('Y-m-d'),
                'avatarUrl'     => $avatarUrl,
                'aboutMe'       => $aboutMe,
                'gender'        => self::getLoggedUser()->gender,
                'visibility'    => self::getLoggedUser()->visibility,
                'showBirthdate' => self::getLoggedUser()->showBirthdate,
                'showEmail'     => self::getLoggeduser()->showEmail,
                'showFacebook'  => self::getLoggedUser()->showFacebook,
                'allowMessage'  => self::getLoggedUser()->allowMessage,
                'gaAllowed'     => self::getLoggedUser()->gaAllowed
            ]))
        ]);
    }

    /**
     * Solicita o cancelamento do google authenticator.
     *
     * @param object $response
     * @param array $args
     *
     * @return object
     */
    public function config_google_remove_POST($response, $args)
    {
        // Mensagem padrão para retorno.
        $data = [
            'error'     => true,
            'message'   => 'Você não possui o Google Authenticator vinculado!'
        ];

        // Perfil do usuário logado.
        $profile = self::getLoggedUser();

        // Verifica se o perfil possui o GA verificado,
        // Caso possua, permite que o mesmo desative a verificação em duas etapas.
        if($profile->gaAllowed && $this->getRepository()->removeGoogleAuthenticator($profile))
            $data = [ 'success' => true ];

        // Responde com os dados de autenticação recebidos.
        return $response->withJson($data);
    }

    /**
     * Solicita a ativação do google authenticator.
     *
     * @param object $response
     * @param array $args
     *
     * @return object
     */
    public function config_google_activate_POST($response, $args)
    {
        // Mensagem de erro ao realizar login.
        $data = [
            'error' => true,
            'message' => 'Você já possui o Google Authenticator vinculado!'
        ];

        // Perfil do usuário logado.
        $profile = self::getLoggedUser();

        // Caso o usuário não possua o google authenticator vinculado,
        // Inicia então o processo para geração do código e autorização do GA.
        if(!$profile->gaAllowed)
        {
            // Verifica se os dados de código foram enviados para serem validados.
            if(!empty($this->post) && isset($this->post['code']))
            {
                // Obtém os dados para vinculação.
                $secret = str_replace(' ', '', $this->post['secret']);
                $code = $this->post['code'];

                // Realiza a verificação do código de vinculação.
                if($this->getRepository()->verifyGoogleAuthenticatorSecret($secret, $code))
                {
                    // Caso seja validado com sucesso, os dados serão vinculados a conta
                    // Do usuário.
                    $profile->gaAllowed = true;
                    $profile->gaSecret = $secret;

                    // Salva as informações na tabela informando os dados de
                    // autenticação do google.
                    $this->getRepository()->save($profile);

                    // Salva um log informando que o authenticator foi vinculado a conta.
                    $this->getRepository()->addLog($profile, 'O', 'Google Authenticator vinculado com sucesso.');

                    // Define como sendo um sucesso a vinculação.
                    $data = ['success' => true];
                }
                else
                {
                    // Define como sendo um sucesso a vinculação.
                    $data = ['error' => true];
                }
            }
            else
            {
                // Gera as informações necessárias para vinculação do google
                // a conta do usuário.
                $data = $this->getRepository()->generateGoogleAuthenticator();
            }
        }

        // Responde com os dados de autenticação recebidos.
        return $response->withJson($data);
    }

    /**
     * Obtém os logs para a conta do jogador.
     *
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function logs_GET($response, $args)
    {
        // Obtém o entitymanager para realizar a consulta
        // no banco de dados.
        $em = $this->getApp()->getEntityManager();

        // Obtém o resultado de dados para informações de logs
        // Apenas os 200 ultimos registros de logs para a conta.
        $profileLogs = $em->createQuery('
            SELECT
                log, profile
            FROM
                Model\ProfileLog log
            INNER JOIN
                log.profile profile
            WHERE
                profile.id = :id
            ORDER BY
                log.dateTime DESC
        ')
        ->setParameter('id', self::getLoggedUser()->id)
        ->setMaxResults(200)
        ->getResult();

        // Varre o retorno e retorna somente as informações necessarias
        // Para os logs da conta.
        $logs = array_map(function($log) {
            return (object)[
                'message'   => $log->message,
                'date'      => $log->dateTime->format('Y-m-d H:i:s'),
                'address'   => $log->address,
            ];
        }, $profileLogs);

        // Retorna o layout com os dados de logs.
        return $this->render($response, 'bracp.profile.logs.tpl', [
            'logs'  => $logs
        ]);
    }

    /**
     * Método para realizar a alteração de uma senha.
     *
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function password_POST($response, $args)
    {
        // Obtém o repositório para fazer as mudanças de senha.
        $changeResponse = $this->getRepository()->changePassword(self::getLoggedUser(),
            $this->post['old'], $this->post['new'], $this->post['cnf']);

        // Se for alterado com sucesso, então, o retorno será == 1
        if($changeResponse === 1)
            return $response->withJson([
                'success'   => true
            ]);

        // Retorna a resposta com o erro de mudança.
        return $response->withJson([
            'error' => true,
            'errorMessage' => $changeResponse
        ]);
    }

    /**
     * Método de verificação do código de ativação do perfil. 
     *
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function verify_POST($response, $args)
    {
        // Obtém o código que será utilizado para a validação
        // da conta.
        $code = $this->post['code'];

        // Obtém o repositorio de dados de verificação de perfil. 
        $profileVerify = $this->getApp()->getEntityManager()->getRepository('Model\ProfileVerify');

        // Verifica se o código é valido, caso não seja,
        // Exibe a mensagem.
        if(!$profileVerify->verifyCode($code, Profile::getLoggedUser()))
            return $response->withJson([
                'error'         => true,
                'errorMessage'  => 'Código de ativação é inválido, já foi utilizado ou expirou.',
            ]);

        // Salva informações de log.
        $this->getRepository()->addLog(Profile::getLoggedUser(),
            'O', 'E-mail verificado com sucesso. Cód.: ' . $code);

        // Retorna verdadeiro caso seja ativo com sucesso.
        return $response->withJson([
            'success'   => true,
        ]);
    }

    /**
     * Método de verificação de código de ativação do perfil via link do navegador.
     *
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function verify_GET($response, $args)
    {
        // Se o código não for recebido, então mensagem de erro.
        if(!isset($args->code))
            throw new AppControllerNotFoundException($this);

        // Realiza o processo de validação da conta usando somente o código
        // de verificação.
        $verifyRepository = $this->getApp()->getEntityManager()->getRepository('Model\ProfileVerify');
        $verifyResult = $verifyRepository->verifyCode($args->code);

        // Retorna verdadeiro caso seja ativo com sucesso.
        return $this->render($response, 'bracp.profile.verify.tpl', [
            'verifyResult'  => $verifyResult,
            'code'          => $args->code,
            'loggedIn'      => self::isLoggedIn(),
        ]);
    }

    /**
     * Método para reenviar os dados de validação para o usuário.
     * 
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function verify_resend_POST($response, $args)
    {
        // Busca no banco de dados o último código de verificação que ainda
        // Não foi confirmado pelo usuário.
        $verifyRepository = $this->getApp()->getEntityManager()->getRepository('Model\ProfileVerify');

        // Verifica se existe algum código de confirmação que ainda
        // Não foi ativo pela conta.
        $resendResult = $verifyRepository->verifyResend(self::getLoggedUser());

        // Informa o resultado do reenvio do código de verificação de perfil.
        return $response->withJson([
            'success' => $resendResult
        ]);
    }

    /**
     * Rota para realizar logout de profile;
     */
    public function logout_POST($response, $args)
    {
        // Realiza o logout do perfil.
        $this->profileLogout();

        return $response->withJson([
            'success' => true
        ]);
    }

    /**
     * Método para realizar login no painel de controle. 
     *
     * @param object $response 
     * @param object $args 
     *
     * @return object Dados de resposta
     */
    public function login_POST($response, $args)
    {
        // Objeto de profile do jogador.
        $profile = null;

        // Obtém o código para verificação do login de usuário...
        if(isset($this->post['gaCode']))
        {
            // Realiza a validação do código para este momento...
            $gaCodeValid = $this->profileLogin(null, $this->post['gaCode']);

            // Retorna o status de validação código de dados.
            return $response->withJson([
                (($gaCodeValid === true) ? 'success' : 'error') => true
            ]);
        }

        // Solicitou login com o facebook
        if(isset($this->post['accessToken']))
            $profile = $this->getRepository()->findByFacebookToken($this->post['accessToken']);
        else
            $profile = $this->getRepository()->findByEmail($this->post['id'], $this->post['pw']);

        // Se o profile for NULL então retorna uma resposta de erro a tentativa de login do usuário
        if(is_null($profile))
            return $response->withJson(['error' => true]);

        // Define os dados de acesso ao profile informado.
        $gaQRCode = $this->profileLogin($profile);

        // Responde com verdadeiro a tentativa de login.
        return $response->withJson([
            'success'       => ($gaQRCode === true),
            'gaInUse'       => ($gaQRCode === false),
        ]);

    }

    /**
     * Método para criação de uma conta com o uso ou de facebook ou de vias normais.
     */
    public function create_POST($response, $args)
    {
        try
        {
            // Cria a instância do objeto para o perfil a ser cadastrado.
            $profile = new \Model\Profile;
            $profile->blocked = false;
            $profile->verified = true;
            $profile->canCreateAccount = true;
            $profile->canReportProfiles = true;
            $profile->visibility = 'M';
            $profile->showBirthdate = 'M';
            $profile->showEmail = 'M';
            $profile->showFacebook = 'M';
            $profile->allowMessage = 'M';
            $profile->privileges = 'U';
            $profile->registerDate = new \DateTime();

            // Se o accessToken foi definido, então está criando uma conta
            // Utilizando o facebook.
            if(isset($this->post['accessToken']))
            {
                // Obtém os dados de informações.
                $accessToken = $this->post['accessToken'];
                $fbResponse = $this->getApp()->getFacebook()->getLoginStatus($accessToken);
                $fbGender = strtolower($fbResponse['gender']);

                // Cria a instância dos dados para cadastro via facebook.
                $profile->name = $fbResponse['name'];
                $profile->gender =  (($fbGender == 'male') ? 'M' : (($fbGender == 'female') ? 'F' : 'O'));
                $profile->birthdate = new \Datetime();
                $profile->facebookId = $fbResponse['id'];
            }
            else
            {
                // Se não houver dados de token, então o cadastro é feito utilizando
                // As vias normais de definição de cadastro.
                $profile->name = $this->post['name'];
                $profile->email = $this->post['email'];
                $profile->password = $this->post['password'];
                $profile->gender = $this->post['gender'];
                $profile->birthdate = new \DateTime($this->post['birthDate']);
            }

            // Salva os dados de profile no banco de dados.
            $this->getRepository()->save($profile);

            // Realiza login automático no perfil criado
            $this->profileLogin($profile);

            // Retorna sucesso como parte de criação de conta.
            return $response->withJson(['success' => true,
                'successMessage' => (
                    (empty($profile->facebookId) && APP_MAILER_ALLOWED && BRACP_ACCOUNT_VERIFY) ?
                        'Seu perfil foi criado, mas é necessário que seja verificado por e-mail. Um e-mail foi enviado para o endereço cadastrado com os dados de verificação.':
                        'Seu perfil foi criado com sucesso!'
                )
            ]);
        }
        catch(\Exception $ex)
        {
            return $response->withJson([
                'error'         => true,
                'errorMessage'  => $ex->getMessage(),
                'trace'         => $ex->getTraceAsString(),
            ]);
        }
    }

    /**
     * Realiza o login do profile no sistema.
     *
     * @param \Model\Profile $profile
     */
    public function profileLogin(\Model\Profile $profile = null, $gaCode = null)
    {
        // Verifica se o perfil que está tentando fazer login
        // foi definido, caso não tenha sido, e o código $gaCode for definido, então
        // é uma tentativa de validação do código...
        if(is_null($profile) && !is_null($gaCode))
        {
            // Repositório.
            $repo = $this->getRepository();

            // Tentativa de validação do código GA.
            $profileId = $this->getApp()->getSession()->BRACP_GA_PROFILE_ID;
            $gaCountErrors = intval($this->getApp()->getSession()->BRACP_GA_PROFILE_ERROR);

            // Encontra o perfil a ser bloqueado.
            $profile = $repo->findById($profileId);

            // Verifica se houve um erro de validação para o código GA.
            if(!$repo->verifyGoogleAuthenticatorProfileId($profile->id, $gaCode))
            {
                // Caso obtenha mais erros que o permitido para o login,
                // Realiza o bloqueio do perfil do usuário.
                if((++$gaCountErrors) >= APP_GOOGLE_AUTH_MAX_ERRORS)
                {
                    // Apaga os registros de memória e bloqueia o perfil por 10 minutos.
                    unset($this->getApp()->getSession()->BRACP_GA_PROFILE_ID);
                    unset($this->getApp()->getSession()->BRACP_GA_PROFILE_ERROR);

                    // Adiciona o timer para bloqueio do acesso.
                    $profile->blockedUntil = time() + BRACP_ACCOUNT_WRONGPASS_BLOCKTIME;
                    $profile->blockedReason = 'Muitas tentativas de acesso incorretas pelo Google Authenticator.';

                    // Salva os dados de perfil para bloqueio.
                    $repo->save($profile);

                    // Adiciona log para bloqueio do perfil.
                    $repo->addLog($profile, 'B', 'Muitas tentativas de acesso incorretas pelo Google Authenticator.');

                    // Perfil bloqueado para login.
                    return null;
                }

                // Caso gere erros de autenticação mas não gere bloqueio de usuário
                // irá gerar novamente o código de autenticação.
                $this->getApp()->getSession()->BRACP_GA_PROFILE_ERROR = $gaCountErrors;

                // Gera o código de autenticação do google para autorizar no aplicativo.
                return false;
            }

            // Caso os dados de perfil sejam autorizados com sucesso com o uso
            // do fator de autorização, então, apaga os dados de memória e permite ao usuário realizar o login
            unset($this->getApp()->getSession()->BRACP_GA_PROFILE_ID);
            unset($this->getApp()->getSession()->BRACP_GA_PROFILE_ERROR);
        }
        // Se, para o login, o perfil solicitar o código GA, irá gerar o mesmo para realizar o login.
        else if(!is_null($profile) && $profile->gaAllowed)
        {
            // Grava em session os dados de autenticação para login
            // de autenticação e verificação das informações.
            if(!isset($this->getApp()->getSession()->BRACP_GA_PROFILE_ID))
            {
                $this->getApp()->getSession()->BRACP_GA_PROFILE_ID = $profile->id;
                $this->getApp()->getSession()->BRACP_GA_PROFILE_ERROR = 0;
            }

            // Gera o código de autenticação do google para autorizar no aplicativo.
            return false;
        }

        // Realiza login automático no perfil criado
        $this->getApp()->getSession()->BRACP_PROFILE_ID = $profile->id;
        self::$loggedUser = $profile;

        // Grava informações de log informando que fez o logout corretamente.
        $this->getRepository()
                ->addLog($profile, 'L', 'Login realizado com sucesso',
                    $this->getApp()->getFirewall()->getIpAddress());

        // Retorna verdadeiro indicando que foi possível o login do usuário.
        return true;
    }

    /**
     * Realiza o logout do profile atual.
     */
    public function profileLogout()
    {
        unset($this->getApp()->getSession()->BRACP_PROFILE_ID);
        self::$loggedUser = null;
    }

    /**
     * Obtém resposta para se o usuário está logado ou não. 
     * @static
     *
     * @return bool Verdadeiro se estiver logado.
     */
    public static function isLoggedIn()
    {
        return isset(\App::getInstance()->getSession()->BRACP_PROFILE_ID);
    }

    /**
     * Obtém o objeto do usuário logado. 
     *
     * @return \Model\Profile
     */
    public static function getLoggedUser()
    {
        // Verifica se o usuário está logado.
        if(!self::isLoggedIn())
            return null;

        // Se já foi carregado uma vez o usuário logado é desnecessário
        // Refazer o query para achar o usuário.
        if(!is_null(self::$loggedUser))
            return self::$loggedUser;

        $app = \App::getInstance();
        // Obtém o profile que está logado no sistema.
        $profile = $app->getEntityManager()
                        ->getRepository('Model\Profile')
                        ->findOneBy(['id' => $app->getSession()->BRACP_PROFILE_ID]);

        // Retorna o usuário logado no sistema.
        return (self::$loggedUser = $profile);
    }

}

