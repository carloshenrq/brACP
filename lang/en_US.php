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

return
[
    /**
     * @refer templates/menu.tpl
     * @author carloshenrq
     */
    'MENU'  => [
        'TITLE'     => 'Menu',
        'HOME'      => 'Home',
        'ADMIN'     => [
            'TITLE'     => 'Administration',
            'BACKUP'    => 'Create Backup',
            'THEMES'    => 'Update Themes',
            'CACHE'     => 'Clear Cache',
            'MODS'      => 'Customizations',
            'PLAYERS'   => 'Players',
            'UPDATE'    => 'System Update',
        ],
        'MYACC'     => [
            'TITLE'         => 'My Account',
            'UNAUTHENTICATED'   => [
                'LOGIN'         => 'Login',
                'CREATE'        => 'Register',
                'CREATE_SEND'   => 'Activation Code',
                'RECOVER'       => 'Recovery',
            ],
            'AUTHENTICATED'     => [
                'CHANGE'    => [
                    'PASS'  => 'Change Password',
                    'MAIL'  => 'Change E-mail',
                ],
                'STORAGE'   => 'Storage',
                'CHARS'     => 'Characters',
                'LOGOUT'    => 'Logout (%s)',
            ],
        ],
        'MERCHANTS' =>  'Merchants',
        'RANKINGS'  =>  [
            'TITLE'     => 'Classifications',
            'PLAYERS'   => 'Players',
            'CHARS'     => 'Characters',
            'ECONOMY'   => 'Economy',
            'WOE'       => 'War of Emperium',
            'CASTLES'   => 'Castles',
            'GUILDS'    => 'Guilds',
        ],
    ],

    /**
     * @refer templates/default.tpl
     * @author carloshenrq
     */
    'DEFAULT'   => [
        'DEVELOP'   => [
            'TITLE'     => 'Warning: the development mode is on!',
            'MESSAGE'   => 'The system is being executed in development mode! Some configurations may not respont properly.',
        ],
        'BETA'      => [
            'TITLE'     => 'You are running a beta version! <i>(%s)</i>',
            'MESSAGE'   => 'The version you are running is not stable and is still in test phase! Please, be aware of any updates since there probably will be bug fixes.',
        ],
        'ADMIN'     => [
            'TITLE'     => 'Administrator Reminder!',
            'MESSAGE'   => 'Some options might not be enabled for administrators for security reasons.',
        ],
    ],

    /**
     * @refer templates/home.tpl
     * @author carloshenrq
     */
    'HOME'      => [
        'TITLE'     => 'Main',
        'MESSAGE'   =>  'Welcome to the Control Panel.'
    ],

    /**
     * @refer templates/account.login.ajax.tpl
     * @author carloshenrq
     */
    'LOGIN'     => [
        'TITLE'     => 'My Account &raquo; Login',

        'ERROR'     => [
            'MISMATCH'  => 'User or password is not correct.',
            'DENIED'    => 'Acess denied. You can not login.',
        ],
        'SUCCESS'   => 'Successfully logged login. Processing...',
        'MESSAGE'   => [
            'HEADER'        => 'To access the management section of your account, it is necessary<br>'.
                                'for you to login before continue.',
            'CREATE'        =>  'Don\'t have an account yet? Click here to create one.',
            'RECOVER'       =>  'Lost your account? Click here to retrieve your account.',
        ],
        'HOLDER'    => [
            'USERID'    => 'Username',
            'PASSWD'    => 'Password',
        ],
        'BUTTONS'   => [
            'SUBMIT'    => 'Login',
            'RESET'     => 'Reset',
            'CLOSE'     => 'Close',
        ],
    ],

    'LOGIN_BUTTONS'             => [
        'SUBMIT'                => 'Submit',
        'RESET'                 => 'Clear',
    ],

    /**
     * @refer templates/account.logout.ajax.tpl
     * @author carloshenrq
     */
    'LOGOUT'    => [
        'TITLE'     => 'My Account &raquo; Sign Out',
        'SUCCESS'   => 'Successfully logged out! Processing...',
    ],

    /**
     * @refer templates/account.register.ajax.tpl
     * @author carloshenrq
     */
    'CREATE'    => [
        'TITLE'     => 'My Account &raquo; Register',
        'ERROR'     => [
            'DISABLED'  => 'Account creation is disabled.',
            'ADMIN_MODE'    => 'Creation of this type of accounts is only possible in Administrator mode.',
            'MISMATCH'  => [
                'PASSWORD'  => 'The passwords you entered don\'t match!',
                'EMAIL'     => 'The email address you entered don\'t match!',
            ],
            'USED'      => 'Username or e-mail is already being used.',
        ],
        'SUCCESS'   => 'Your account was created successfully! You can already login.',
        'MESSAGE'   => [
            'HEADER'    => 'To create an account it is necessary for you to provide the following information correctly, this will allow you to access the game and the panel functions.',
        ],
        'MAIL'      => [
            'TITLE' => 'Account Registered',
        ],
        'HOLDER'    => [
            'USERID'                => 'Username',
            'PASSWORD'              => 'Password',
            'PASSWORD_CONFIRM'      => 'Confirm Password',
            'MALE'                  => 'Male',
            'FEMALE'                => 'Female',
            'EMAIL'                 => 'E-mail Address',
            'EMAIL_CONFIRM'         => 'Confirm E-mail Address',
            'ACCEPT_TERMS'          => 'I\'ve read and agree to the terms of use for this server.',
        ],
        'BUTTONS'   => [
            'SUBMIT'    => 'Register',
            'CLOSE'     => 'Close'
        ],
    ],

    /**
     * @refer templates/account.register.resend.ajax.tpl
     * @author carloshenrq
     */
    'RESEND'    => [
        'TITLE'     => 'My Account &raquo; Activation Code',
        'ERROR'     => [
            'DISABLED'  => 'Account activation code is disabled.',
            'NOACC'     => 'Invalid information to resend the confirmation code.',
            'USED'      => 'Confirmation code was already used or is invalid.',
        ],
        'MAIL'      => [
            'TITLE_CONFIRM'     => 'Confirm your Register',
            'TITLE_CONFIRMED'   => 'Account Confirmed',
        ],
        'SUCCESS'   => 'Confirmation code was successfully sent to the registered e-mail.',
        'CONFIRMED' => 'Your account was activated. You can already login.',
        'MESSAGE'   => [
            'HEADER_NO_CODE'    => 'To resend the activation code for your account you must enter your Username and e-mail adrress.',
            'HEADER_HAS_CODE'   => 'Since you already have your activation code, you need to enter it to confirm your account.',
        ],
        'HOLDER'    => [
            'USERID'    => 'Username',
            'EMAIL'     => 'Registered E-mail',
            'CODE'      => 'Activation Code',
            'HAS_CODE'  => 'I already have the activation code!',
        ],
        'BUTTONS'   => [
            'SUBMIT'    => 'Resend',
            'CONFIRM'   => 'Confirm',
            'RESET'     => 'Reset',
            'CLOSE'     => 'Close',
        ],
    ],

    /**
     * @refer templates/rankings.chars.ajax.tpl
     * @author carloshenrq
     */
    'RANKINGS'      => [
        'NO_CHARS'  => 'No characters.',

        'CHARS' => [
            'TITLE'     => 'Rankings &raquo; Characters &raquo; Overall',
            'CAPTION'   => 'Top %s players',
        ],

        'ECONOMY'   => [
            'TITLE'     => 'Rankings &raquo; Characters &raquo; Economy',
            'CAPTION'   => 'Top %s richest players',
        ],

        'TABLE' => [
            'POSIT'     => 'Pos.',
            'NAME'      => 'Name',
            'LEVEL'     => 'Level',
            'CLASS'     => 'Class',
            'STATUS'    => 'Status',
            'ZENY'      => 'Zeny',
        ],
    ],

    /**
     * @refer templates/account.storage.ajax.tpl
     * @author carloshenrq
     */
    'STORAGE'       => [
        'TITLE'     => 'My Account &raquo; Storage',
        'SUBTITLE'  => 'Storage',

        'ERROR'     => [
            'NO_ITEMS'  => 'You don\'t have any item that can be displayed.',
        ],

        'MESSAGE'   => [
            'HEADER'    => 'Here is the list of all items in your storage.',
        ],
    ],

    /**
     * @refer templates/account.recover.ajax.tpl
     * @author carloshenrq
     */
    'RECOVER'       => [
        'TITLE'     => 'My Account &raquo; Recovery',
        'ERROR'     => [
            'DISABLED'  => 'Account recovery is disabeld.',
            'MISMATCH'  => 'Combination of user and email not found.',
            'USED'      => 'Recovery code is invalid or was already used.',
            'OTHER'     => 'It was not possible to recovery your password.',
        ],
        'CONFIRMED'     => 'Your new password was sent to your e-mail.',
        'MAIL'      => [
            'TITLE_CODE'     => 'Recovery Code',
            'TITLE_SEND'     => 'Username Recovery',
        ],
        'SUCCESS'   => [
            'CODE'  => 'A recovery code was sent to your e-mail.',
            'SEND'  => 'Information about your account was sent to your e-mail.',
        ],
        'MESSAGE'   => [
            'HEADER_NO_CODE'    => 'To recover your usename you must first give us the correct information below.',
            'HEADER_HAS_CODE'   => 'If you already have the recovery code, enter it so we can send you a new password.',
        ],
        'HOLDER'    => [
            'USERID'    => 'Username',
            'EMAIL'     => 'E-mail Address',
            'CODE'      => 'Recovery Code',
            'HAS_CODE'  => 'I already have the Recovery Code.',
        ],
        'BUTTONS'   => [
            'SUBMIT'    => 'Recover',
            'CONFIRM'   => 'Confirm',
            'CLOSE'     => 'Close',
        ],
    ],

    /**
     * @refer templates/account.chars.tpl
     * @author carloshenrq
     */
    'CHARS'     => [
        'TITLE' => 'My Account &raquo; Characters',

        'ERROR' => [
            'NO_CHAR'   => 'You don\'t have any character to display. Try to login and create a new character!'
        ],

        'MESSAGE'   => 'Below there is the list of all the characters you have in the game. Here you can reset their position, equipments or their appearance.',

        'SUCCESS'   => [
            'POSIT'     => 'Position was reseted sucessfully!',
            'APPEAR'    => 'Appearance was reseted sucessfully!',
            'EQUIP'     => 'Equipments were reseted sucessfully!'
        ],

        'TABLE'     => [
            'NAME'          => 'Name',
            'CLASS'         => 'Class',
            'PARTY'         => 'Party',
            'GUILD'         => 'Guild',
            'LEVEL'         => 'Level',
            'POSIT_NOW'     => 'Position',
            'POSIT_SAVE'    => 'Return',
            'ZENY'          => 'Zeny',
            'STATUS'        => 'Status',
            'ACTION'        => 'Reset Information',
            'NO_PARTY'      => 'No Group',
            'NO_GUILD'      => 'No Guild'
        ],

        'BUTTONS'   => [
            'RESET_POSIT'   => 'Position',
            'RESET_APPEAR'  => 'Appearance',
            'RESET_EQUIP'   => 'Equips',
        ]
    ],

    /**
     * @refer templates/account.change.password.ajax.tpl
     * @author carloshenrq
     */
    'CHANGEPASS'    =>  [
        'TITLE' => 'My Account &raquo; Change Password',

        'ERROR'    =>  [
            'NOADMIN'       => 'Administrators aren\'t able to change their passwords.',

            'MISMATCH1'     => 'Current password does\'t match.',
            'MISMATCH2'     => 'New passwords doesn\'t match.',
            'EQUALS'        => 'Your new password can\'t be the same as your current one.',
            'OTHER'         => 'An error occured during the process of changing your password.',
        ],

        'MESSAGE'   =>  [
            'ADMIN' => '<strong>Note:</strong> For security reasons it is recommended to disable password changes for administrator accounts.',

            'HEADER'    => 'To change your password it is necessary for you to enter your current password, your desired new password and confirm it.',
        ],

        'SUCCESS'   =>  'Your password was sucessfully updated!',

        'HOLDER'    =>  [
            'ACTUAL_PASSWORD'   => 'Current Password',
            'NEW_PASSWORD'      => 'Enter your new password',
            'CONFIRM_PASSWORD'  => 'Confirm your new password',
        ],

        'BUTTONS'   => [
            'SUBMIT'    => 'Update',
            'CLOSE'     => 'Close',
        ],

        'MAIL'      => [
            'TITLE' => 'Notification: Password Update',
        ],
    ],

    /**
     * @refer templates/account.change.mail.ajax.tpl
     * @author carloshenrq
     */
    'CHANGEMAIL'    => [
        'TITLE' => 'My account &raquo; Change E-mail',

        'ERROR' => [
            'DISABLED'  => 'E-mail change is disabled.',
            'NOADMIN'   => 'Administrators aren\'t able to change their e-mail addresses.',

            'MISMATCH1' => 'Current e-mail doesn\'t match.',
            'MISMATCH2' => 'New e-mails doesn\'t match.',
            'EQUALS'    => 'New e-mail address can\'t be the same as the current one.',
            'DELAY'     => 'You can\'t change your e-mail right now. Please try again later.',
            'TAKEN'     => 'This e-mail address is already in use.',
            'OTHER'     => 'There was an error during the process of changing your e-mail.',
        ],

        'SUCCESS'   => 'Your e-mail address was sucessfully updated!.',

        'MESSAGE'   => [
            'HEADER'    => 'To change your e-mail address it is necessary for you to enter your current e-mail, your desired new one and confirm it.',
        ],

        'HOLDER'    => [
            'EMAIL'     => 'Current E-mail',
            'NEW_EMAIL' => 'New E-mail',
            'CONFIRM'   => 'Confirm your new E-mail',
        ],

        'BUTTONS'   => [
            'SUBMIT'    => 'Update',
            'CLOSE'     => 'Close',
        ],

        'MAIL'  => [
            'TITLE' => 'Notification: E-mail Update',
        ],
    ],

    // Tradução dos arquivos administrativos.
    'ADMIN'     => [
        /**
         * @refer templates/admin.players.ajax.tpl
         * @author carloshenrq
         */
        'PLAYER'   => [
            'TITLE' => 'Administration &raquo; Players',
        ],

        /**
         * @refer templates/admin.update.ajax.tpl
         * @author carloshenrq
         */
        'UPDATE'   => [
            'TITLE' => 'Administration &raquo; System Update',

            'WARNING'   => [
                'TITLE'     => 'Warning!',
                'MESSAGE'   => 'After the update is done, it will be needed to reinstall the control panel.<br>'.
                                'There are many things that need to be applied, it is generally easier to do a clean install.<br>'.
                                '<br>'.
                                'Only start the update process if you are sure you want to continue.',
            ],
            'VERSION'   => '<strong>Current Version:</strong> %s',
            'TABLE'     => [
                'CAPTION'       => 'Stable Versions',
                'COLUMNS'       => [
                    'DESCRIPTION'   => 'Description',
                    'VERSION'       => 'Version',
                    'DATE'          => 'Date',
                    'FILE'          => 'File',
                    'ACTION'        => 'Action',
                ],

                'PRE_RELEASE'   => 'Pre-release',
                'INSTALL'       => 'Install',
            ],
        ],

        /**
         * @refer templates/admin.backup.ajax.tpl
         * @author carloshenrq
         */
        'BACKUP'    => [
            'TITLE' => 'Administration &raquo; Backup',
            'MESSAGE'   => [
                'SUCCESS'   => 'The backup file was created sucessfully!'.
                               'Name: <strong>%s</strong> with <strong>%d</strong> files of <strong>%s</strong> size.',
                'CHEKCSUM'  => 'Checksum:',
                'MD5'       => 'md5',
                'SHA1'      => 'sha1',
                'SHA512'    => 'sha512',
            ],
        ],
    ],

    /**
     * Mensagens de alerta.
     */
    'WARNING'   => [
        'CACHE_ON'  => 'All information in this page can take a few moments to be udpated!',

        // issue #11 - Informativos de Formatação de Campos
        'PATTERN'   => [
            'NORMAL'    => 'Only letters and numbers. Between 4 and 32 characters.',
            'SPECIAL'   => 'Letters, numbers, spaces and special characters (@ $ # &#37; & * !). Between 4 and 32 characters.',
            'ALL'       => 'No restrictions. Between 4 and 32 characters.',
        ]
    ],

    /**
     * Mensagens de erro padrão.
     */
    'ERRORS'    => [
        'RECAPTCHA' => 'Verification code invalid. Please verify.',
        'NO_MOBILE' => 'Mobile Acesss unavaiable.',
        'MAINTENCE' => 'In maintenance.',

        'REGEXP'    => 'REGEX pattern failed in some of field. Please try again.',

        'NOT_FOUND' => [
            'TITLE'         => 'Ops! Looks like the page you were looking for isn\'t avaiable!',
            'MESSAGE'       => 'x____x\' sorry but the page you were looking for was not found...<br>'.
                                'You can go back to our home page clicking <span class="url-link" data-href="'.BRACP_DIR_INSTALL_URL.'">here</span>.',
        ],

        'ACCESS'    => [
            'TITLE'         => 'Acess Denied',
            'DENIED'        => 'You don\'t have access of this local or the requested page doesn\'t exist!',
            'NEED_LOGOUT'   => 'You can\'t be logged in to perform this action.<br>'.
                                'To logout, <span class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/logout" data-target=".bracp-body">click here</span>.',
            'NEED_SIGNIN'   => 'You need to be logged in to perform this action.<br>'.
                                'Para login, <label class="fake-link" for="modal-login">click here</label>.',
        ],

    ],

    'VENDING'   => [
        'TITLE'     => 'Online Merchants',
        'MESSAGE'   => 'The list of all merchants online and all the items that are being sold by them.',

        'NO_VENDING'    => 'There isn\'t any merchant online with his shop open.',
    ],

    /**
     *
     *
     * Configuração para e-mail.
     *
     *
     */
    'MAIL'  => [

        /**
         * @refer templates/mail.default.tpl
         * @author carloshenrq
         */
        'TITLE' => 'Hello, <strong>%s</strong>.<br>',

        'MESSAGE'   => [
            'FOOTER'    => '<i>This e-mail was sent from <strong>%s</strong> '.
                            'through the request made by the ip address <strong>%s</strong> '.
                            'at <strong>%s</strong>.<br>'.
                            'If it was not you who made this request, please disregard this message.</i>',
        ],

        /**
         * @refer templates/mail.recover{.code}.tpl
         * @author carloshenrq
         */
        'RECOVER'   => [
            /**
             * @refer templates/mail.recover.tpl
             * @author carloshenrq
             */
            'MESSAGE'   => 'Your password was recovered with sucess. Here is your password to login:<br>'.
                            '<br>'.
                            'Password: <strong>%s</strong><br>',
                            '<br>'.
                            'Use this password to login from now on.',
            /**
             * @refer templates/mail.recover.code.tpl
             * @author carloshenrq
             */
            'CODE'      => 'An attempt password recovery was held in your account.<br>'.
                            'To confirm this attempt, please, use the recovery code in the control panel.<br>'.
                            '<br>'.
                            '<strong>Recovery Code</strong>: <u>%1$s</u><br>'.
                            '<i>Code is valid until <strong>%2$s</strong>.</i><br>'.
                            '<br>'.
                            'To apply this recovery code you need to access the menu: <i><strong>My Account &raquo; Recovery</strong></i>.<br>'.
                            '<br>'.
                            'After entering the recovery code, you will receive your new password by email.',
        ],
        /**
         * @refer templates/mail.create{.code}.tpl
         * @author carloshenrq
         */
        'CREATE'    => [
            /**
             * @refer templates/mail.create.tpl
             * @author carloshenrq
             */
            'MESSAGE'   => 'We are grateful for your register and hope you enjoy our server!',

            /**
             * @refer templates/mail.create.code.tpl
             * @author carloshenrq
             */
            'CODE'      => 'To confirm the account creation you need to confirm you e-mail address, by entering the following code on the control panel.<br>'.
                            '<br>' .
                            '<strong>Activation Code:</strong> <u>%1$s</u><br>'.
                            '<i>Code is valid until <strong>%2$s</strong>.</i><br>'.
                            '<br>'.
                            'To apply this activation code you need to access the menu: <i><strong>My Account &raquo; Activation Code</strong></i>.<br>'.
                            '<br>'.
                            'After activating your account, you will receive a message when the server accepted your account.',
            /**
             * @refer templates/mail.create.code.success.tpl
             * @author carloshenrq
             */
            'ACTIVATED' => 'Your account was activated successfully! You can already login.'
        ],

        /**
         * @refer templates/mail.change.password.tpl
         * @author carloshenrq
         */
        'CHANGEPASS'    => [
            'MESSAGE'   => 'This e-mail is just a notification message to inform you that your password was changed.',
        ],

        /**
         * @refer templates/mail.change.mail.tpl
         * @author carloshenrq
         */
        'CHANGEMAIL'    => [
            'MESSAGE'   => 'This e-mail is just a notification message to inform you that your e-mail was changed.<br>'.
                            'Old E-mail: <strong>%s</strong><br>'.
                            'New E-mail: <strong>%s</strong><br>',
        ],
    ],

    'SERVER_STATUS' =>  [
        'SERVER'    => 'Server',
        'TEXT'      => 'State',
        'PLAYER'    => 'Online Players',

        'LOADING'   => 'Loading server information...',

        'STATE'     => [
            0       => 'Offline',
            1       => 'Online',
        ],
    ],

    'FOOTER'    => [
        'LANGUAGE'  => 'Language',
        'THEME'     => 'Theme',
        'ADDRESS'   => 'IP',
        'NAVIGATOR' => 'Browser',
    ],

    /**
     * Define o status do jogador com o texto formatado.
     */
    'STATUS' => [
        0 => 'Disconnected',
        1 => 'Conected',
    ],

    'ITEM'  =>  [
        'NOT_IDENTIFY'  => 'Not Identified',
        'TYPE'  => [
            0 => 'Usable (Healing)',
            2 => 'Usable',
            3 => 'Not Usable',
            4 => 'Equipment',
            5 => 'Weapon',
            6 => 'Card',
            7 => 'Egg',
            8 => 'Pet Acessory',
            10 => 'Ammunition',
            11 => 'Usable (Delay)',
            12 => 'Shadow Equipment',
            18 => 'Usable',
        ],
    ],

    /**
     * Lista de classes para exibição no painel de controle.
     * 
     * @author Megasantos
     * @link https://github.com/Megasantos/Fluxcp/blob/master/config/jobs.php
     *
     * @var array
     */
    'JOBS' => [
        0    => 'Novice',
        1    => 'Swordsman',
        2    => 'Mage',
        3    => 'Archer',
        4    => 'Acolyte',
        5    => 'Merchant',
        6    => 'Thief',
        7    => 'Knight',
        8    => 'Priest',
        9    => 'Wizard',
        10   => 'Blacksmith',
        11   => 'Hunter',
        12   => 'Assassin',
        //13   => 'Knight (Mounted)',
        14   => 'Crusader',
        15   => 'Monk',
        16   => 'Sage',
        17   => 'Rogue',
        18   => 'Alchemist',
        19   => 'Bard',
        20   => 'Dancer',
        //21   => 'Crusader (Mounted)',
        22   => 'Wedding',
        23   => 'Super Novice',
        24   => 'Gunslinger',
        25   => 'Ninja',
        26   => 'Xmas',
        27   => 'Summer',
        28   => 'Hanbok',
        29   => 'Oktoberfest',

        4001 => 'High Novice',
        4002 => 'High Swordsman',
        4003 => 'High Mage',
        4004 => 'High Archer',
        4005 => 'High Acolyte',
        4006 => 'High Merchant',
        4007 => 'High Thief',
        4008 => 'Lord Knight',
        4009 => 'High Priest',
        4010 => 'High Wizard',
        4011 => 'Whitesmith',
        4012 => 'Sniper',
        4013 => 'Assassin Cross',
        //4014 => 'Lord Knight (Mounted)',
        4015 => 'Paladin',
        4016 => 'Champion',
        4017 => 'Professor',
        4018 => 'Stalker',
        4019 => 'Creator',
        4020 => 'Clown',
        4021 => 'Gypsy',
        //4022 => 'Paladin (Mounted)',

        4023 => 'Baby',
        4024 => 'Baby Swordsman',
        4025 => 'Baby Mage',
        4026 => 'Baby Archer',
        4027 => 'Baby Acolyte',
        4028 => 'Baby Merchant',
        4029 => 'Baby Thief',
        4030 => 'Baby Knight',
        4031 => 'Baby Priest',
        4032 => 'Baby Wizard',
        4033 => 'Baby Blacksmith',
        4034 => 'Baby Hunter',
        4035 => 'Baby Assassin',
        //4036 => 'Baby Knight (Mounted)',
        4037 => 'Baby Crusader',
        4038 => 'Baby Monk',
        4039 => 'Baby Sage',
        4040 => 'Baby Rogue',
        4041 => 'Baby Alchemist',
        4042 => 'Baby Bard',
        4043 => 'Baby Dancer',
        //4044 => 'Baby Crusader (Mounted)',
        4045 => 'Super Baby',
        
        4046 => 'Taekwon',
        4047 => 'Star Gladiator',
        //4048 => 'Star Gladiator (Flying)',
        4049 => 'Soul Linker',

        4050 => 'Jiang Shi',
        4051 => 'Death Knight',
        4052 => 'Dark Collector',

        4054 => 'Rune Knight',
        4055 => 'Warlock',
        4056 => 'Ranger',
        4057 => 'Arch Bishop',
        4058 => 'Mechanic',
        4059 => 'Guillotine Cross',
        4060 => 'Rune Knight+',
        4061 => 'Warlock+',
        4062 => 'Ranger+',
        4063 => 'Arch Bishop+',
        4064 => 'Mechanic+',
        4065 => 'Guillotine Cross+',
        4066 => 'Royal Guard',
        4067 => 'Sorcerer',
        4068 => 'Minstrel',
        4069 => 'Wanderer',
        4070 => 'Sura',
        4071 => 'Genetic',
        4072 => 'Shadow Chaser',
        4073 => 'Royal Guard+',
        4074 => 'Sorcerer+',
        4075 => 'Minstrel+',
        4076 => 'Wanderer+',
        4077 => 'Sura+',
        4078 => 'Genetic+',
        4079 => 'Shadow Chaser+',

        //4080 => 'Rune Knight (Mounted)',
        //4081 => 'Rune Knight+ (Mounted)',
        //4082 => 'Royal Guard (Mounted)',
        //4083 => 'Royal Guard+ (Mounted)',
        //4084 => 'Ranger (Mounted)',
        //4085 => 'Ranger+ (Mounted)',
        //4086 => 'Mechanic (Magic Gear)',
        //4087 => 'Mechanic+ (Magic Gear)',

        4096 => 'Baby Rune Knight',
        4097 => 'Baby Warlock',
        4098 => 'Baby Ranger',
        4099 => 'Baby Arch Bishop',
        4100 => 'Baby Mechanic',
        4101 => 'Baby Guillotine Cross',
        4102 => 'Baby Royal Guard',
        4103 => 'Baby Sorcerer',
        4104 => 'Baby Minstrel',
        4105 => 'Baby Wanderer',
        4106 => 'Baby Sura',
        4107 => 'Baby Genetic',
        4108 => 'Baby Shadow Chaser',
        
        //4109 => 'Baby Rune Knight (Mounted)',
        //4110 => 'Baby Royal Guard (Mounted)',
        //4111 => 'Baby Ranger (Mounted)',
        //4112 => 'Baby Mechanic (Magic Gear)',
        
        4190 => 'Expanded Super Novice',
        4191 => 'Expanded Super Baby',
        
        4211 => 'Kagerou',
        4212 => 'Oboro',
        
        4215 => 'Rebellion'
    ],
];
