<?php

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //users
        //confirm email
        $template = Template::firstOrCreate([
            'key'  => 'confirm_email',
            'type' => '1',
        ], [
            'name'        => 'Confirm Email',
            'subject'     => '{{{app_name}}}: Verify your online account.',
            'subject_esp' => '{{{app_name}}}: Verify your online account.',
            'subject_pap' => '{{{app_name}}}: Verify your online account.',
            'content'     => 'Welcome {{{user_name}}},<br><br>

Please verify your account by clicking below on the button/link - <br><br>

<a href="{{{verification_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Confirm email address
</a>
<br><br>
Your referred by client is "{{{referred_by_name}}}".
<br><br>
            ',
            'content_esp' => 'Bienvenido {{{user_name}}},<br><br>

Verifique su cuenta haciendo clic a continuación en el botón / enlace - <br><br>

<a href="{{{verification_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Verifique su correo electrónico
</a>
<br><br>
Your referred by client is "{{{referred_by_name}}}".
<br><br>
            ',
            'content_pap' => 'Bon biní {{{user_name}}},<br><br>

Verifiká bo kuenta i klek riba e boton /link -<br><br>

<a href="{{{verification_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Verifiká bo email
</a>
<br><br>
Your referred by client is "{{{referred_by_name}}}".
<br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'All Users',
            'params'    => 'app_name, user_name, verification_link, referred_by_name',
        ]);


        //confirm email with password
        $template = Template::firstOrCreate([
            'key'  => 'confirm_email_password',
            'type' => '1',
        ], [
            'name'        => 'Confirm Email With Password',
            'subject'     => '{{{app_name}}}: Verify your online account.',
            'subject_esp' => '{{{app_name}}}: Verify your online account.',
            'subject_pap' => '{{{app_name}}}: Verify your online account.',
            'content'     => 'Welcome {{{user_name}}},<br><br>

Please verify your account by clicking below on the button/link -<br><br>

<a href="{{{verification_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Confirm email address
</a>
<br><br>
You can access your account with the password: <b>{{{password}}}</b>
<br><br>
Your referred by client is "{{{referred_by_name}}}".
<br><br>
            ',
            'content_esp' => 'Bienvenido {{{client_name}}},<br><br>

Verifique su cuenta haciendo clic a continuación en el botón / enlace -<br><br>

<a href="{{{verification_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Verifique su correo electrónico
</a>
<br><br>
Puede acceder a su cuenta con la contraseña: <b>{{{password}}}</b>
<br><br>
Your referred by client is "{{{referred_by_name}}}".
<br><br>
            ',
            'content_pap' => 'Bon biní {{{client_name}}},<br><br>

Verifiká bo kuenta i klek riba e boton /link -<br><br>

<a href="{{{verification_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Verifiká bo email
</a>
<br><br>
Bo por aksesa bo kuenta ku e password: <b>{{{password}}}</b><br><br>
Your referred by client is "{{{referred_by_name}}}".
<br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'All Users',
            'params'    => 'app_name, user_name, verification_link, password, referred_by_name',
        ]);

        $template = Template::firstOrCreate([
            'key'  => 'confirm_merchant_password',
            'type' => '1',
        ], [
            'name'        => 'Confirm Merchant Email With Password',
            'subject'     => '{{{app_name}}}: Verify your online account.',
            'subject_esp' => '{{{app_name}}}: Verify your online account.',
            'subject_pap' => '{{{app_name}}}: Verify your online account.',
            'content'     => 'Welcome {{{merchant_name}}},<br><br>

Please verify your account by clicking below on the button/link -<br><br>

<a href="{{{verification_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Confirm email address
</a>
<br><br>
You can access your account with the password: <b>{{{password}}}</b>
<br><br>
            ',
            'content_esp' => 'Bienvenido {{{client_name}}},<br><br>

Verifique su cuenta haciendo clic a continuación en el botón / enlace -<br><br>

<a href="{{{verification_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Verifique su correo electrónico
</a>
<br><br>
Puede acceder a su cuenta con la contraseña: <b>{{{password}}}</b>
<br><br>
            ',
            'content_pap' => 'Bon biní {{{client_name}}},<br><br>

Verifiká bo kuenta i klek riba e boton /link -<br><br>

<a href="{{{verification_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Verifiká bo email
</a>
<br><br>
Bo por aksesa bo kuenta ku e password: <b>{{{password}}}</b>
<br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'All Merchants',
            'params'    => 'app_name, merchant_name, verification_link, password',
        ]);


        //confirm web registered email
        $template = Template::firstOrCreate([
            'key'  => 'confirm_web_email',
            'type' => '1',
        ], [
            'name'        => 'Confirm Web Registered Email',
            'subject'     => '{{{app_name}}}: Verify your online account.',
            'subject_esp' => '{{{app_name}}}: Verify your online account.',
            'subject_pap' => '{{{app_name}}}: Verify your online account.',
            'content'     => 'Welcome {{{user_name}}},<br><br>

Welcome to {{{app_name}}}.<br><br>

You have just created a demo account for Hylawallet. To activate your demo-account we request you to verify your demo-account by clicking below on the button / link-<br><br>

<a href="{{{verification_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Confirm email address
</a><br><br>
Your referred by client is "{{{referred_by_name}}}".
<br><br>
For your safety and security
<br><br>
We will delete your demo account if you do not verify and activate your demo account within 48 hours of submitting through our online application form.<br><br>
            ',
            'content_esp' => 'Bienvenido {{{user_name}}},
<br><br>
Bienvenido a {{{app_name}}}.
<br><br>
Acaba de crear una cuenta demo para Hylawallet. Para activar su cuenta de demostración, le solicitamos que verifique su cuenta de demostración haciendo clic a continuación en el botón / enlace -
<br><br>
<a href="{{{verification_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Verifique su correo electrónico
</a>
<br><br>
Your referred by client is "{{{referred_by_name}}}".
<br><br>
Para su seguridad y protección
<br><br>
Eliminaremos su cuenta de demostración si no verifica y activa su cuenta de demostración dentro de las 48 horas posteriores al envío a través de nuestro formulario de solicitud en línea.
<br><br>            ',
            'content_pap' => 'Bon biní {{{user_name}}},
<br><br>
Bon biní, na {{{app_name}}}.
<br><br>
Ba kaba di krea un kuenta di demonstrashon ku Hylawallet. Pa aktivá bo kuenta di demonstrashon nos ta pidibo verifiká bo kuenta di demonstrashon hasiendo klek riba e boton -
<br><br>
<a href="{{{verification_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Verifiká bo email
</a>
<br><br>
Your referred by client is "{{{referred_by_name}}}".
<br><br>
Pa bo seguridat i protekshon
<br><br>
Nos lo elimina bo kuenta di demonstrashon si bo no veremailsifiká i aktivá bo kuenta denter di 48 ora ku ba aplika atraves di nos formulario online.
<br><br>            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'app_name, user_name, verification_link, referred_by_name',
        ]);


        //confirm web registered email with password
        $template = Template::firstOrCreate([
            'key'  => 'confirm_web_email_password',
            'type' => '1',
        ], [
            'name'        => 'Confirm Web Registered Email With Password',
            'subject'     => '{{{app_name}}}: Verify your online account.',
            'subject_esp' => '{{{app_name}}}: Verify your online account.',
            'subject_pap' => '{{{app_name}}}: Verify your online account.',
            'content'     => 'Welcome {{{user_name}}},
<br><br>
Welcome to {{{app_name}}}.
<br><br>
You have just created a demo account for Hylawallet. To activate your demo-account we request you to verify your demo-account by clicking below on the button / link-
<br><br>
<a href="{{{verification_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Confirm email address
</a>
<br><br>
You can access your demo account with the password: {{{password}}}.
<br><br>
Your referred by client is "{{{referred_by_name}}}".
<br><br>
For your safety and security
<br><br>
We will delete your demo account if you do not verify and activate your demo account within 48 hours of submitting through our online application form.
<br><br>            ',
            'content_esp' => 'Bienvenido {{{user_name}}},
<br><br>
Bienvenido a {{{app_name}}}.
<br><br>
Acaba de crear una cuenta demo para Hylawallet. Para activar su cuenta de demostración, le solicitamos que verifique su cuenta de demostración haciendo clic a continuación en el botón / enlace -
<br><br>
<a href="{{{verification_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Verifique su correo electrónico
</a>
<br><br>
Puede acceder a su cuenta de demostración con la contraseña: {{{password}}}.
<br><br>
Your referred by client is "{{{referred_by_name}}}".
<br><br>
Para su seguridad y protección
<br><br>
Eliminaremos su cuenta de demostración si no verifica y activa su cuenta de demostración dentro de las 48 horas posteriores al envío a través de nuestro formulario de solicitud en línea.
<br><br>            ',
            'content_pap' => 'Bon biní {{{user_name}}},
<br><br>
Bon biní, na {{{app_name}}}.
<br><br>
Ba kaba di krea un kuenta di demonstrashon ku Hylawallet. Pa aktivá bo kuenta di demonstrashon nos ta pidibo verifiká bo kuenta di demonstrashon hasiendo klek riba e boton -
<br><br>
<a href="{{{verification_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Verifiká bo email
</a>
<br><br>
Bo por yega na bo kuenta ku e password: {{{password}}}.
<br><br>
Your referred by client is "{{{referred_by_name}}}".
<br><br>
Pa bo seguridat i protekshon
<br><br>
Nos lo elimina bo kuenta di demonstrashon si bo no veremailsifiká i aktivá bo kuenta denter di 48 ora ku ba aplika atraves di nos formulario online.
<br><br>            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'app_name, user_name, verification_link, password, referred_by_name',
        ]);

        //Delete User Mail
        $template = Template::firstOrCreate([
            'key'  => 'delete_user_mail',
            'type' => '1',
        ], [
            'name'        => 'Delete User Mail',
            'subject'     => '{{{app_name}}}: Account Deleted.',
            'subject_esp' => '{{{app_name}}}: Account Deleted.',
            'subject_pap' => '{{{app_name}}}: Account Deleted.',
            'content'     => 'Dear {{{user_name}}},
<br><br>
Your account has been deleted.<br><br>
            ',
            'content_esp' => 'Estimado {{{user_name}}},<br><br>

Your account has been deleted.<br><br>
            ',
            'content_pap' => 'Estimado {{{user_name}}},<br><br>

Your account has been deleted.<br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'app_name, user_name',
        ]);


        //loans
        //loan on hold and declined mail
        $template = Template::firstOrCreate([
            'key'  => 'loan_on_hold_declined_status',
            'type' => '1',
        ], [
            'name'        => 'Loan On Hold / Declined Mail',
            'subject'     => '{{{app_name}}}: Action required.',
            'subject_esp' => '{{{app_name}}}: acción requerida.',
            'subject_pap' => '{{{app_name}}}: akshon rekerí.',
            'content'     => 'Dear {{{user_name}}},<br><br>

Your Application with ID {{{loan_id}}} for "{{{loan_reason}}}" has a new status: "{{{status}}}".<br><br>

Reason: {{{reason}}}<br>
Description: {{{description}}}<br><br>
            ',
            'content_esp' => 'Estimado {{{user_name}}},<br><br>

Su solicitud con ID {{{loan_id}}} por el motivo "{{{reason}}}" se ha dejado en "{{{status}}}".<br><br>

Motivo: {{{reason}}}<br>
Descripción: {{{description}}}<br><br>
            ',
            'content_pap' => 'Estimado {{{user_name}}},<br><br>

Bo solisitut pa fiansa ku ID {{{loan_id}}} pa e motibu "{{{reason}}}" a keda pone na "{{{status}}}".<br><br>

Motibu: {{{reason}}}<br>
Deskripshon: {{{description}}}<br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'app_name, user_name, loan_id, loan_reason, status, reason, description',
        ]);

        //loan current mail
        $template = Template::firstOrCreate([
            'key'  => 'loan_current_status',
            'type' => '1',
        ], [
            'name'        => 'Loan Current Mail',
            'subject'     => '{{{app_name}}}: Your loan application has a new status: "{{{status}}}".',
            'subject_esp' => '{{{app_name}}}: Su aplicacion tiene un nuevo estado: "{{{status}}}".',
            'subject_pap' => '{{{app_name}}}: Bo aplikashon tin un status nobo: "{{{status}}}".',
            'content'     => 'Dear {{{user_name}}},<br><br>

Your Loan (ID {{{loan_id}}}) has been approved.<br><br>
<a href="{{{pagare_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Pagare
</a>
            ',
            'content_esp' => 'Estimado {{{user_name}}},<br><br>

Su préstamo (ID {{{loan_id}}}) ha sido aprobada.<br><br>
<a href="{{{pagare_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Pagare
</a>

            ',
            'content_pap' => 'Estimado {{{user_name}}},<br><br>

Bo fiansa (ID {{{loan_id}}}) ta aproba.<br><br>
<a href="{{{pagare_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Pagare
</a>

            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'app_name, user_name, loan_id, pagare_link',
        ]);

        //loan Pre approved mail
        $template = Template::firstOrCreate([
            'key'  => 'loan_pre_approved_status',
            'type' => '1',
        ], [
            'name'        => 'Loan Pre Approved Mail',
            'subject'     => '{{{app_name}}}: Your loan application has a new status: "{{{status}}}".',
            'subject_esp' => '{{{app_name}}}: Su aplicacion tiene un nuevo estado: "{{{status}}}".',
            'subject_pap' => '{{{app_name}}}: Bo aplikashon tin un status nobo: "{{{status}}}".',
            'content'     => 'Dear {{{user_name}}},<br><br>

Your application with ID {{{loan_id}}} for reason “{{{loan_reason}}}” has been pre-approved.<br><br>

In order to complete you loan application we request you to visit one of our offices to finalize the creation of your account. <br><br>
You need to bring the following documents:<br><br>

<ul>
    <li>Valid ID</li>
    <li>Proof of salary</li>
    <li>Last 2 paystubs</li>
    <li>Proof of address</li>
</ul>
            ',
            'content_esp' => 'Estimado {{{user_name}}},<br><br>

Su solicitud con ID {{{loan_id}}} por la razón "{{{loan_reason}}}" ha sido pre aprobado.<br><br>

Para completar su solicitud de préstamo, le solicitamos que visite una de nuestras oficinas para finalizar la creación de su cuenta.<br><br>
Necesitas traer los siguientes documentos:<br><br>

<ul>
    <li>Identificación válida</li>
    <li>Constancia de Salario</li>
    <li>Últimos 2 recibos de sueldo</li>
    <li>Prueba de domicilio</li>
</ul>
            ',
            'content_pap' => 'Estimado {{{user_name}}},<br><br>

Bo solisitut pa fiansa ku ID :loan_id pa e motibu “:reason” a keda aprobá.<br><br>

Pa kompletá bo solisitut pa e fiansa bishita un di nos ofisinanan pa finalisá bo kuenta.<br><br>
E dokumentonan nesesario:<br><br>

<ul>
    <li>Identifikashon Valido</li>
    <li>Delaster dos slepnan di salario</li>
    <li>Last 2 paystubs</li>
    <li>Prueba di bo adrès</li>
</ul>
            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'app_name, user_name, loan_id, loan_reason',
        ]);

        //loan other mail
        $template = Template::firstOrCreate([
            'key'  => 'loan_status',
            'type' => '1',
        ], [
            'name'        => 'Loan Other Mail',
            'subject'     => '{{{app_name}}}: Your loan application has a new status: "{{{status}}}".',
            'subject_esp' => '{{{app_name}}}: Su aplicacion tiene un nuevo estado: "{{{status}}}".',
            'subject_pap' => '{{{app_name}}}: Bo aplikashon tin un status nobo: "{{{status}}}".',
            'content'     => 'Dear {{{user_name}}},<br><br>

Your Application with ID {{{loan_id}}} for "{{{loan_reason}}}" has a new status: "{{{status}}}".<br><br>
            ',
            'content_esp' => 'Estimado {{{user_name}}},<br><br>

Su solicitud con ID {{{loan_id}}} por el motivo "{{{reason}}}" se ha dejado en "{{{status}}}".<br><br>
            ',
            'content_pap' => 'Estimado {{{user_name}}},
<br><br>
Bo solisitut pa fiansa ku ID {{{loan_id}}} pa e motibu "{{{reason}}}" a keda pone na "{{{status}}}".<br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'app_name, user_name, loan_id, loan_reason, status',
        ]);


        //credits
        //credit bank transfer mail
        $template = Template::firstOrCreate([
            'key'  => 'credit_bank_transfer',
            'type' => '1',
        ], [
            'name'        => 'Credit Bank Transfer',
            'subject'     => '{{{app_name}}}: Your transfer to bank request is in "{{{status}}}" state.',
            'subject_esp' => '{{{app_name}}}: Your transfer to bank request is in "{{{status}}}" state.',
            'subject_pap' => '{{{app_name}}}: Your transfer to bank request is in "{{{status}}}" state.',
            'content'     => 'Dear {{{user_name}}},<br><br>

Your bank transfer request (ID {{{credit_id}}}) has a new status: "{{{status}}}".<br><br>
            ',
            'content_esp' => 'Estimado {{{user_name}}},<br><br>

Su solicitud de transferencia bancaria (ID {{{credit_id}}}) tiene un nuevo estado: "{{{status}}}".<br><br>
            ',
            'content_pap' => 'Estimado {{{user_name}}},
<br><br>
Bo petishon pa un transferensia di banko (ID {{{credit_id}}}) tin un status nobo: "{{{status}}}".<br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'app_name, user_name, credit_id, status',
        ]);

        //credit cash payout mail
        $template = Template::firstOrCreate([
            'key'  => 'credit_cash_payout',
            'type' => '1',
        ], [
            'name'        => 'Credit Cash Payout',
            'subject'     => '{{{app_name}}}: Your cash payout request has a new status: "{{{status}}}".',
            'subject_esp' => '{{{app_name}}}: Su solicitud de pago en efectivo tiene un nuevo estado: "{{{status}}}".',
            'subject_pap' => '{{{app_name}}}: Bo petishon pa kesh payout tin un status nobo: "{{{status}}}".',
            'content'     => 'Dear {{{user_name}}},<br><br>

Your cash payout request (ID {{{credit_id}}}) has a new status: "{{{status}}}".<br><br>
            ',
            'content_esp' => 'Estimado {{{user_name}}},
<br><br>
Su solicitud de pago en efectivo (ID {{{credit_id}}}) tiene un nuevo estado: "{{{status}}}".<br><br>
            ',
            'content_pap' => 'Estimado {{{user_name}}},
<br><br>
Bo petishon pa kesh payout (ID {{{credit_id}}}) tin un status nobo: "{{{status}}}".<br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'app_name, user_name, credit_id, status',
        ]);

        //Audit Report Late Mail
        $template = Template::firstOrCreate([
            'key'  => 'audit_report_late_mail',
            'type' => '1',
        ], [
            'name'        => 'Audit Report Late Mail',
            'subject'     => '{{{app_name}}}: Audit approve remains pending.',
            'subject_esp' => '{{{app_name}}}: Audit approve remains pending.',
            'subject_pap' => '{{{app_name}}}: Audit approve remains pending.',
            'content'     => 'Dear {{{super_admin_name}}},<br><br>

Audit report approval process remains pending for below clients by auditors.<br><br>

<table>
    <thead>
    <tr>
        <th>Date</th>
        <th>User</th>
        <th>Branch</th>
        <th>Country</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    {{{client_data}}}
    </tbody>
</table>
            ',
            'content_esp' => 'Estimado {{{super_admin_name}}},<br><br>

Audit report approval process remains pending for below clients by auditors.<br><br>

<table>
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Usuario</th>
        <th>Sucursal</th>
        <th>País</th>
        <th>Cantidad</th>
    </tr>
    </thead>
    <tbody>
    {{{client_data}}}
    </tbody>
</table>
            ',
            'content_pap' => 'Estimado {{{super_admin_name}}},<br><br>

Audit report approval process remains pending for below clients by auditors.<br><br>

<table>
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Usuario</th>
        <th>Branch</th>
        <th>Pais</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    {{{client_data}}}
    </tbody>
</table>
            ',
        ]);
        $template->update([
            'receivers' => 'Super Admin',
            'params'    => 'app_name, super_admin_name, client_data',
        ]);


        //Raffle
        //Raffle Winner
        $template = Template::firstOrCreate([
            'key'  => 'raffle_winner',
            'type' => '1',
        ], [
            'name'        => 'Raffle Winner',
            'subject'     => '{{{app_name}}}: Raffle Winner.',
            'subject_esp' => '{{{app_name}}}: GANADOR.',
            'subject_pap' => '{{{app_name}}}: GANADOR.',
            'content'     => 'Dear {{{user_name}}},<br><br>

We are pleased to inform you that you are the lucky winner of this month\'s raffle.<br><br>

Please contact us at {{{mobile_no}}} or email {{{email}}} to claim your prize.<br><br>

Congratulations!<br><br>
            ',
            'content_esp' => 'Estimado {{{user_name}}},<br><br>

Con mucho agrado le informamos que usted es el feliz ganador de la rifa del presente mes.<br><br>

Favor contactarse con nosotros al teléfono {{{mobile_no}}} o bien al correo {{{email}}} para retirar su premio.<br><br>

¡Felicidades!<br><br>
            ',
            'content_pap' => 'Estimado {{{user_name}}},<br><br>

Nos ta kontentu di anunsiá ku bo ta e felis ganadó di e rifa di e luna akí!<br><br>

Por fabor tuma kontakto ku nos na Tel: {{{mobile_no}}} òf email nos na {{{email}}} pa reklamá bo premio.<br><br>

Pabien!<br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'app_name, user_name, mobile_no, email',
        ]);

        //Raffle Looser
        $template = Template::firstOrCreate([
            'key'  => 'raffle_looser',
            'type' => '1',
        ], [
            'name'        => 'Raffle Looser',
            'subject'     => '{{{app_name}}}: Raffle Winner.',
            'subject_esp' => '{{{app_name}}}: GANADOR.',
            'subject_pap' => '{{{app_name}}}: GANADOR.',
            'content'     => 'Dear {{{user_name}}},<br><br>

We inform you that you did not win the raffle this month.<br><br>

We wish you luck in next month’s raffle. <br><br>

We are at your service.<br><br>
            ',
            'content_esp' => 'Estimado {{{user_name}}},
<br><br>
Le informamos que no resultó ganador de la rifa del presente mes, esperamos que. 
<br><br>
en el próximo mes resulte ganador.
<br><br>
Estamos a su servicio.<br><br>
            ',
            'content_pap' => 'Estimado {{{user_name}}},<br><br>

Nos ke informá bo ku lamentablemente bo no ta e felis ganadó di e rifa di e luna akí.<br><br>

Suerte ku e rifa di e siguiente luna!<br><br>

Saludo kordial,<br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'app_name, user_name',
        ]);

        //Raffle Reminder
        $template = Template::firstOrCreate([
            'key'  => 'raffle_reminder',
            'type' => '1',
        ], [
            'name'        => 'Raffle Reminder',
            'subject'     => '{{{app_name}}}: Reminder raffle.',
            'subject_esp' => '{{{app_name}}}: Reminder raffle.',
            'subject_pap' => '{{{app_name}}}: Reminder raffle.',
            'content'     => 'Dear {{{user_name}}},<br><br>

REMINDER: {{{app_name}}} the raffle is tomorrow...<br><br>
            ',
            'content_esp' => 'Estimado {{{user_name}}},<br><br>

RECORDATORIO: {{{app_name}}} la rifa es mañana...<br><br>
            ',
            'content_pap' => 'Estimado {{{user_name}}},<br><br>

REKORDATORIO: {{{app_name}}} E rifa mensual ta manan atrobe.<br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'app_name, user_name',
        ]);

        //Referral Benefits Mail
        $template = Template::firstOrCreate([
            'key'  => 'referral_mail',
            'type' => '1',
        ], [
            'name'        => 'Referral Benefits Mail',
            'subject'     => 'Referral Code Benefits',
            'subject_esp' => 'Referral Code Benefits',
            'subject_pap' => 'Referral Code Benefits',
            'content'     => 'Dear {{{client_name}}},<br><br>

Your referral code for {{{app_name}}} is {{{referral_code}}}.<br><br>

With every loan one of your referents make, you will receive the following benefits as below:<br><br>

<table>
    <thead>
    <tr>
        <th>Title</th>
        <th>Referrals</th>
        <th>Pay Per Loan Start</th>
        <th>Pay Per Loan PIF</th>
    </tr>
    </thead>
    <tbody>
    {{{benefits_data}}}
    </tbody>
</table>
            ',
            'content_esp' => 'Estimado {{{client_name}}},<br><br>

Your referral code for {{{app_name}}} is {{{referral_code}}}.<br><br>

With every loan one of your referents make, you will receive the following benefits as below:<br><br>

<table>
    <thead>
    <tr>
        <th>Title</th>
        <th>Referrals</th>
        <th>Pay Per Loan Start</th>
        <th>Pay Per Loan PIF</th>
    </tr>
    </thead>
    <tbody>
    {{{benefits_data}}}
    </tbody>
</table>
            ',
            'content_pap' => 'Estimado {{{client_name}}},<br><br>

Your referral code for {{{app_name}}} is {{{referral_code}}}.<br><br>

With every loan one of your referents make, you will receive the following benefits as below:<br><br>

<table>
    <thead>
    <tr>
        <th>Title</th>
        <th>Referrals</th>
        <th>Pay Per Loan Start</th>
        <th>Pay Per Loan PIF</th>
    </tr>
    </thead>
    <tbody>
    {{{benefits_data}}}
    </tbody>
</table>
            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name, referral_code, benefits_data',
        ]);


        //birth day Mail
        $template = Template::firstOrCreate([
            'key'  => 'birthday_mail',
            'type' => '1',
        ], [
            'name'        => 'Birth Day',
            'subject'     => 'Happy Birthday',
            'subject_esp' => 'Happy Birthday',
            'subject_pap' => 'Happy Birthday',
            'content'     => 'Dear {{{client_name}}},<br><br>

{{{app_name}}} wishes you happy birthday.<br><br>
            ',
            'content_esp' => 'Estimado {{{client_name}}},<br><br>

{{{app_name}}} wishes you happy birthday.<br><br>
            ',
            'content_pap' => 'Estimado {{{client_name}}},<br><br>

{{{app_name}}} wishes you happy birthday.<br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name',
        ]);

        //Transaction add mail
        $template = Template::firstOrCreate([
            'key'  => 'payment_confirmation_mail',
            'type' => '1',
        ], [
            'name'        => 'Payment Confirmation Mail',
            'subject'     => 'Payment Confirmation Mail',
            'subject_esp' => 'Payment Confirmation Mail',
            'subject_pap' => 'Payment Confirmation Mail',
            'content'     => 'Dear {{{client_name}}},<br><br>

Your Payment is confirmed. Your receipt is given below.<br><br>

<a href="{{{receipt_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Receipt
</a><br><br>
            ',
            'content_esp' => 'Estimado {{{client_name}}},<br><br>

Your Payment is confirmed. Your receipt is given below.<br><br>

<a href="{{{receipt_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Confirm email address
</a><br><br>
            ',
            'content_pap' => 'Estimado {{{client_name}}},<br><br>

Your Payment is confirmed. Your receipt is given below.<br><br>

<a href="{{{receipt_link}}}" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
    Confirm email address
</a><br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name, receipt_link',
        ]);

        //Reminder before  mail
        $template = Template::firstOrCreate([
            'key'  => 'loan_default_before_reminder_mail',
            'type' => '1',
        ], [
            'name'        => 'Loan Default Before Reminder Mail',
            'subject'     => 'Default Reminder Mail',
            'subject_esp' => 'Default Reminder Mail',
            'subject_pap' => 'Default Reminder Mail',
            'content'     => 'Dear {{{client_name}}},<br><br>

Your loan with id {{{loan_id}}} which has {{{outstanding_balance}}} will be moved to default status tomorrow.<br><br>
            ',
            'content_esp' => 'Estimado {{{client_name}}},<br><br>

Your loan with id {{{loan_id}}} which has {{{outstanding_balance}}} will be moved to default status tomorrow.<br><br>
            ',
            'content_pap' => 'Estimado {{{client_name}}},<br><br>

Your loan with id {{{loan_id}}} which has {{{outstanding_balance}}} will be moved to default status tomorrow.<br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name, loan_id, outstanding_balance',
        ]);

        //Reminder After Mail
        $template = Template::firstOrCreate([
            'key'  => 'loan_default_after_reminder_mail',
            'type' => '1',
        ], [
            'name'        => 'Loan Default After Reminder Mail',
            'subject'     => 'Default Reminder Mail',
            'subject_esp' => 'Default Reminder Mail',
            'subject_pap' => 'Default Reminder Mail',
            'content'     => 'Dear {{{client_name}}},<br><br>

Your loan with id {{{loan_id}}} which has {{{outstanding_balance}}} is in default status.<br><br>
            ',
            'content_esp' => 'Estimado {{{client_name}}},<br><br>

Your loan with id {{{loan_id}}} which has {{{outstanding_balance}}} is in default status.<br><br>
            ',
            'content_pap' => 'Estimado {{{client_name}}},<br><br>

Your loan with id {{{loan_id}}} which has {{{outstanding_balance}}} is in default status.<br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name, loan_id, outstanding_balance',
        ]);

        $template = Template::firstOrCreate([
            'key'  => 'email_footer',
            'type' => '3',
        ], [
            'name'        => 'Email Footer',
            'subject'     => '',
            'subject_esp' => '',
            'subject_pap' => '',
            'content'     => 'You\'re receiving this email because you signed up for {{{app_name}}}.<br><br>
Please do not reply to this email. Emails sent to this address will not be answered.<br><br>
Copyright © 2018 {{{app_name}}}.<br><br>
All rights reserved.<br><br>
            ',
            'content_esp' => 'You\'re receiving this email because you signed up for {{{app_name}}}.<br><br>
Por favor no responder a este email. Los correos electrónicos enviados a esta dirección no serán contestados.<br><br>
Copyright © 2018 {{{app_name}}}.<br><br>
Todos los derechos reservados.<br><br>
            ',
            'content_pap' => 'You\'re receiving this email because you signed up for {{{app_name}}}.<br><br>
Por fabor no kontesta e email aki, ya ku e no lo wordu kontesta.<br><br>
Copyright © 2018 {{{app_name}}}.<br><br>
Tur derechi ta reserva.<br><br>
            ',
        ]);
        $template->update([
            'receivers' => 'All mails',
            'params'    => 'app_name',
        ]);


        //Push Messages
        //loan current status changes
        $template = Template::firstOrCreate([
            'key'  => 'loan_current_status',
            'type' => '2',
        ], [
            'name'        => 'Loan Current Status',
            'subject'     => 'Updates',
            'subject_esp' => 'Actualizaciones',
            'subject_pap' => 'Updates',
            'content'     => 'Your Loan (ID {{{loan_id}}}) has been approved.',
            'content_esp' => 'Su préstamo (ID {{{loan_id}}}) ha sido aprobada.',
            'content_pap' => 'Bo fiansa (ID {{{loan_id}}}) ta aproba.',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name, loan_id',
        ]);

        //loan other status changes
        $template = Template::firstOrCreate([
            'key'  => 'loan_status',
            'type' => '2',
        ], [
            'name'        => 'Loan Status',
            'subject'     => 'Updates',
            'subject_esp' => 'Actualizaciones',
            'subject_pap' => 'Updates',
            'content'     => 'Your Application with ID {{{loan_id}}} for "{{{reason}}}" has a new status: "{{{status}}}".',
            'content_esp' => 'Su solicitud con ID {{{loan_id}}} por el motivo "{{{reason}}}" se ha dejado en "{{{status}}}".',
            'content_pap' => 'Bo solisitut pa fiansa ku ID {{{loan_id}}} pa e motibu "{{{reason}}}" a keda pone na "{{{status}}}".',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name, loan_id, reason, status',
        ]);


        //credit bank transfer status
        $template = Template::firstOrCreate([
            'key'  => 'credit_bank_transfer',
            'type' => '2',
        ], [
            'name'        => 'Credit Bank Transfer',
            'subject'     => 'Updates',
            'subject_esp' => 'Actualizaciones',
            'subject_pap' => 'Updates',
            'content'     => 'Your bank transfer request (ID {{{credit_id}}}) has a new status: "{{{status}}}".',
            'content_esp' => 'Su solicitud de transferencia bancaria (ID {{{credit_id}}}) tiene un nuevo estado: "{{{status}}}".',
            'content_pap' => 'Bo petishon pa un transferensia di banko (ID {{{credit_id}}}) tin un status nobo: "{{{status}}}".',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name, credit_id, status',
        ]);

        //credit cash payout status
        $template = Template::firstOrCreate([
            'key'  => 'credit_cash_payout',
            'type' => '2',
        ], [
            'name'        => 'Credit Cash Payout',
            'subject'     => 'Updates',
            'subject_esp' => 'Actualizaciones',
            'subject_pap' => 'Updates',
            'content'     => 'Your cash payout request (ID {{{credit_id}}}) has a new status: "{{{status}}}".',
            'content_esp' => 'Su solicitud de pago en efectivo (ID {{{credit_id}}}) tiene un nuevo estado: "{{{status}}}".',
            'content_pap' => 'Bo petishon pa kesh payout (ID {{{credit_id}}}) tin un status nobo: "{{{status}}}".',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name, credit_id, status',
        ]);

        //Raffle Winner
        $template = Template::firstOrCreate([
            'key'  => 'raffle_winnner',
            'type' => '2',
        ], [
            'name'        => 'Raffle winner',
            'subject'     => '{{{app_name}}}: Reminder raffle.',
            'subject_esp' => '{{{app_name}}}: Reminder raffle.',
            'subject_pap' => '{{{app_name}}}: Reminder raffle.',
            'content'     => 'We are pleased to inform you that you are the lucky winner of this month\'s raffle.',
            'content_esp' => 'Con mucho agrado le informamos que usted es el feliz ganador de la rifa del presente mes.',
            'content_pap' => 'Nos ta kontentu di anunsiá ku bo ta e felis ganadó di e rifa di e luna akí!',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name',
        ]);

        //Raffle Looser
        $template = Template::firstOrCreate([
            'key'  => 'raffle_looser',
            'type' => '2',
        ], [
            'name'        => 'Raffle Looser',
            'subject'     => '{{{app_name}}}: Reminder raffle.',
            'subject_esp' => '{{{app_name}}}: Reminder raffle.',
            'subject_pap' => '{{{app_name}}}: Reminder raffle.',
            'content'     => 'We inform you that you did not win the raffle this month.',
            'content_esp' => 'Le informamos que no resultó ganador de la rifa del presente mes, esperamos que ',
            'content_pap' => 'Nos ke informá bo ku lamentablemente bo no ta e felis ganadó di e rifa di e luna akí.',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name',
        ]);

        //Raffle Reminder
        $template = Template::firstOrCreate([
            'key'  => 'raffle_reminder',
            'type' => '2',
        ], [
            'name'        => 'Raffle Reminder',
            'subject'     => '{{{app_name}}}: Reminder raffle.',
            'subject_esp' => '{{{app_name}}}: Reminder raffle.',
            'subject_pap' => '{{{app_name}}}: Reminder raffle.',
            'content'     => 'REMINDER: {{{app_name}}} the raffle is tomorrow...',
            'content_esp' => 'RECORDATORIO: {{{app_name}}} la rifa es mañana...',
            'content_pap' => 'REKORDATORIO: {{{app_name}}} E rifa mensual ta manan atrobe.',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name',
        ]);

        //Raffle Reminder
        $template = Template::firstOrCreate([
            'key'  => 'birthday_message',
            'type' => '2',
        ], [
            'name'        => 'Birthday Message',
            'subject'     => 'Happy Birthday',
            'subject_esp' => 'Happy Birthday',
            'subject_pap' => 'Happy Birthday',
            'content'     => '{{{app_name}}} wishes you happy birthday.',
            'content_esp' => '{{{app_name}}} wishes you happy birthday.',
            'content_pap' => '{{{app_name}}} wishes you happy birthday.',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name',
        ]);

        $template = Template::firstOrCreate([
            'key'  => 'payment_confirmation_message',
            'type' => '2',
        ], [
            'name'        => 'Payment Confirmation Message',
            'subject'     => 'Payment Confirmation Message',
            'subject_esp' => 'Payment Confirmation Message',
            'subject_pap' => 'Payment Confirmation Message',
            'content'     => 'Your payment is confirmed.',
            'content_esp' => 'Your payment is confirmed.',
            'content_pap' => 'Your payment is confirmed.',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name',
        ]);

        //Reminder before message
        $template = Template::firstOrCreate([
            'key'  => 'loan_default_before_reminder_message',
            'type' => '2',
        ], [
            'name'        => 'Loan Default Before Reminder Message',
            'subject'     => 'Default Reminder Message',
            'subject_esp' => 'Default Reminder Message',
            'subject_pap' => 'Default Reminder Message',
            'content'     => 'Your loan with id {{{loan_id}}} which has {{{outstanding_balance}}} will be moved to default status tomorrow.',
            'content_esp' => 'Your loan with id {{{loan_id}}} which has {{{outstanding_balance}}} will be moved to default status tomorrow.',
            'content_pap' => 'Your loan with id {{{loan_id}}} which has {{{outstanding_balance}}} will be moved to default status tomorrow.',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name, loan_id, outstanding_balance',
        ]);

        //Reminder after message
        $template = Template::firstOrCreate([
            'key'  => 'loan_default_after_reminder_message',
            'type' => '2',
        ], [
            'name'        => 'Loan Default After Reminder Message',
            'subject'     => 'Default Reminder Message',
            'subject_esp' => 'Default Reminder Message',
            'subject_pap' => 'Default Reminder Message',
            'content'     => 'Your loan with id {{{loan_id}}} which has {{{outstanding_balance}}} is in default state.',
            'content_esp' => 'Your loan with id {{{loan_id}}} which has {{{outstanding_balance}}} is in default state.',
            'content_pap' => 'Your loan with id {{{loan_id}}} which has {{{outstanding_balance}}} is in default state.',
        ]);
        $template->update([
            'receivers' => 'Client',
            'params'    => 'client_name, app_name, loan_id, outstanding_balance',
        ]);
    }
}
