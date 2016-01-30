<?
namespace Extasy\Users;

use \Extasy\Api\ApiController;
use \CConfig;
use \Email_Controller;
use \Sitemap_Sample;
use \Sitemap;
use \RuntimeException;
use \Extasy\Users\admin\UpdateEmailConfig;
use \Extasy\Users\admin\UpdatePasswordConfig;
use \Extasy\Users\forgot\Dashboard\EmailConfig;
use \Extasy\Users\profile\Dashboard\DeleteProfileEmailConfig;
use \Extasy\Users\registration\Dashboard\Email;
use \Extasy\Users\registration\Dashboard\EmailConfirmation;
use \Extasy\Users\registration\Dashboard\PageConfig as RegistrationPageConfig;
use \Extasy\Users\login\Dashboard\PageConfig as LoginPageConfig;

class UsersModule
{
    /**
     *
     */
    public static function initAPI()
    {
        $api = ApiController::getInstance();
        $api->Add(new \Extasy\Users\Api\ChangeEmail());
        $api->add(new \Extasy\Users\Api\GetUserInfoOperation());
        $api->add(new \Extasy\Users\Api\UpdatePasswordOperation());
        $api->add(new \Extasy\Users\Api\UpdateProfileOperation());
        $api->add(new \Extasy\Users\Api\UpdateAvatarOperation());
        $api->add(new \Extasy\Users\Api\IsLoginedOperation());

        $api->add(new \Extasy\Users\Api\RemoveAvatarOperation());
        $api->add(new \Extasy\Users\Api\IsUserExistsOperation());
        $api->add(new \Extasy\Users\Api\DeleteProfile());

        $api->add(new \Extasy\Users\Social\Api\Facebook\Registration());
        $api->add(new \Extasy\Users\Social\Api\Facebook\GetCurrentSession());
        $api->add(new \Extasy\Users\Social\Api\Facebook\Login());

        $api->add(new \Extasy\Users\Social\Api\Twitter\Registration());
        $api->add(new \Extasy\Users\Social\Api\Twitter\GetCurrentSession());
        $api->add(new \Extasy\Users\Social\Api\Twitter\Login());

        $api->add(new \Extasy\Users\Social\Api\Vkontakte\Registration());
        $api->add(new \Extasy\Users\Social\Api\Vkontakte\GetCurrentSession());
        $api->add(new \Extasy\Users\Social\Api\Vkontakte\Login());

        $api->add(new \Extasy\Users\Social\Api\Odnoklassniki\Registration());
        $api->add(new \Extasy\Users\Social\Api\Odnoklassniki\GetCurrentSession());
        $api->add(new \Extasy\Users\Social\Api\Odnoklassniki\Login());
    }

    public static function install() {
        UpdateEmailConfig::install();
        UpdatePasswordConfig::install();
        EmailConfig::install();
        DeleteProfileEmailConfig::install();
        Email::install();
        EmailConfirmation::install();

        //
        RegistrationPageConfig::install();
        LoginPageConfig::install();
    }

    public static function installPage( $schemaName, $path, $urlKey, $dashboardUrl,  $pageTitle)
    {
        $sitemapInfo = Sitemap_Sample::getScriptByAdminInfo($path,$dashboardUrl);
        if ( empty( $sitemapInfo )) {
            Sitemap::addScript( $pageTitle, $path, $urlKey,0, $dashboardUrl );
        }
        //
        $config = CConfig::createSchema( $schemaName );
        $config->updateSchema( $schemaName, $pageTitle);
        $config->addControl('seo_title', 'inputfield', 'SEO=title');
        $config->addControl('seo_keywords', 'inputfield', 'SEO=keywords', array('rows' => 10));
        $config->addControl('seo_description', 'inputfield', 'SEO=description', array('rows' => 10));
        $config->setTabsheets(
            array(
                'SEO' => array('seo_title', 'seo_keywords', 'seo_description')
            )
        );
    }

    public static function installEmail($schemaName, $title)
    {
        $config = CConfig::createSchema($schemaName);

        $config->updateSchema($schemaName, $title);
        //
        $config->addControl('subject', 'inputfield', '', array(), 'subject');
        $config->addControl('content', 'htmlfield', '', array(), 'content');

        $config->setTabsheets(
            array(
                'Шаблон письма' => ['subject', 'content'],
            )
        );
    }
    public static function uninstallEmail( $schemaName ) {
        $schema = CConfig::getSchema( $schemaName );
        $schema->delete();
    }
    public static function sendEmail($emailData, $schemaName)
    {
        $schema = CConfig::getSchema($schemaName);

        $values = $schema->getValues();
        $schemaValid = isset( $values['subject'] ) || isset( $values['content']);
        if ( !$schemaValid ) {
            $error = sprintf('Incorrect schema `%s`, not enough fields.', $schemaName);
            throw new RuntimeException( $error );
        }

        try {
            Email_Controller::parseAndSend($emailData['email'], $values['subject'], $values['content'], $emailData);
        } catch (\MailException $e) {

        }
    }
    public static function getMeta( $schemaName ) {
        $config = CConfig::getSchema( $schemaName );
        return $config->getValues();
    }
}


?>