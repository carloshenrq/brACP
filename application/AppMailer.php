<?php

/**
 * Classe para envio de e-mails.
 */
class AppMailer extends AppComponent
{
    /**
     * Construtor para a classe de envio dos e-mails.
     *
     * @param App $app Aplicação relacionada.
     */
    public function __construct(App $app)
    {
        // Faz a chamada do construtor herdado e logo após cria a tabela
        // Para armazenar os envios.
        parent::__construct($app);
    }

    /**
     * Envia o e-mail para o endereço informado 
     * Tratando o template.
     *
     * @param string $subject Assunto
     * @param array $to Destinatário
     * @param string $template Template que será usado pra renderização
     * @param array $data Dados a serem enviados ao renderizador.
     * @param array $attachments Array com todos os anexos a serem inclusos no e-mail.
     * @param array $replaces Array com todos os replaces de string.
     *
     * @return bool Verdadeiro se for enviado com sucesso.
     */
    public function send($subject, $to, $template, $data = [], $attachments = [], $replaces = [])
    {
        if(!APP_MAILER_ALLOWED) // Se a configuração não permite o envio, não será enviado
            return false;

        $asset = new \Controller\Asset($this->getApp(), [], [], []);
        $css = $asset->getCssFile('app.mail.scss');
        $css .= ' ' . $asset->getCssFile('app.message.scss');

        // Inicia o processo de gravação das informações no banco de dados.
        $stmt_mail = $this->getApp()->getSqlite()->prepare('
            INSERT INTO
                mail_send
            VALUES
                (NULL, :Subject, "", :TimeSend, 0);
        ');
        $stmt_mail->execute([
            ':Subject'  => $subject,
            ':TimeSend' => time()
        ]);
        $mailId = $this->getApp()->getSqlite()->lastInsertId();

        // Renderiza o template com os dados a serem enviados.
        $content = $this->getApp()->getView()->render($template, array_merge($data, [
            'css'       => $css,
            'subject'   => $subject,
            'urlSender' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'],
            'ipAddress' => $this->getApp()->getFirewall()->getIpAddress(),
        ]));

        // Transporte para o email.
        $transport = Swift_SmtpTransport::newInstance(APP_MAILER_HOST, APP_MAILER_PORT, APP_MAILER_ENCRYPT)
                                            ->setUsername(APP_MAILER_USER)
                                            ->setPassword(APP_MAILER_PASS);

        // Mailer para envio dos dados.
        $mailer = Swift_Mailer::newInstance($transport);

        // Grava os destinatários na tabela para o envio.
        foreach($to as $mail)
        {
            $stmt_send = $this->getApp()->getSqlite()->prepare('
                INSERT INTO
                    mail_send_destination
                VALUES
                    (:MailID, :Email)
            ');

            $stmt_send->execute([
                ':MailID'   => $mailId,
                ':Email'    => $mail
            ]);
        }

        // Mensagem para enviar.
        $message = Swift_Message::newInstance($subject)
                                    ->setFrom([APP_MAILER_FROM => APP_MAILER_NAME])
                                    ->setTo($to);

        // Varre todos os cases para replace de dados
        // E Aplica o replace de string no texto.
        foreach($replaces as $replace => $dataReplace)
        {
            $_tmp = null;

            if(is_callable($dataReplace)) $_tmp = $dataReplace();
            else $_tmp = $dataReplace;

            $content = str_replace($replace, $_tmp, $content);
        }

        // Atualiza informações do banco de dados preenchendo conteúdo.
        $stmt_mail = $this->getApp()->getSqlite()->prepare('
            UPDATE
                mail_send
            SET
                Body = :Body
            WHERE
                MailID = :MailID
        ');
        $stmt_mail->execute([
            ':Body'     => $content,
            ':MailID'   => $mailId
        ]);

        // Define o conteúdo da mensagem.
        $message->setBody($content, 'text/html');

        $stmt_attach = $this->getApp()->getSqlite()->prepare('
            INSERT INTO
                mail_send_attach
            VALUES
                (NULL, :MailID, :File)
        ');

        // Adiciona os anexos ao e-mail para envio.
        foreach($attachments as $attachName => $attachment)
        {
            $message->attach(Swift_Attachment::fromPath($attachment)->setFilename($attachName));
            $stmt_attach->execute([
                ':MailID'   => $mailId,
                ':FilePath' => $attachment,
                ':File'     => $attachName
            ]);
        }

        $send = false;
        try
        {
            // Obtém o retorno para o envio da mensagem.
            $send = $mailer->send($message) > 0;

            $stmt_update = $this->getApp()->getSqlite()->prepare('
                UPDATE
                    mail_send
                SET
                    SuccessSend = :SuccessSend
                WHERE
                    MailID = :MailID
            ');
            $stmt_update->execute([
                ':SuccessSend'  => $send,
                ':MailID'       => $mailId
            ]);
        }
        catch(\Exception $ex)
        {
            // O que fazer com o erro ao enviar o e-mail?
            // Não atualizar nada?
        }

        // Envia a mensagem ao dono do endereço e-mail.
        return $send;
    }

}

