<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\SetupPage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Constants\GlobalConst;

class SetupPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pages =  ["Home" => "/","About" => "/about","Donation"=>"/donation","Gallery" => "/gallery","Events" => "/events","Video" => "/video","Contact" => "/contact"];
        $data = [];
        foreach($pages as $item => $url) {
            $data[] = [
                'type'         => Str::slug(GlobalConst::SETUP_PAGE),
                'slug'         => Str::slug($item),
                'title'        => json_encode(
                    [
                        'title'     => $item
                    ],
                ),
                'url'          => $url,
                'last_edit_by' => 1,
                'created_at'   => now(),
            ];
        }
        SetupPage::insert($data);

        $details =  [
            "language" => [
                  "en" => [
                     "details" => "Privacy Policy for Fund Collection

                     At Fund Collection, we are committed to safeguarding your privacy and ensuring the security of your personal information. This Privacy Policy outlines our practices concerning the collection, use, and protection of the data you provide to us when using our platform.

                     1. Information We Collect:
                     We may collect personal information, such as your name, contact details, and payment information, when you make a donation or set up a fundraising campaign on our platform. We also collect non-personal information, like your IP address and browsing behavior, to improve our services.

                     2. Use of Information:
                     We use your personal information to process donations, provide you with receipts, and facilitate communication with campaign organizers or donors. We may also use non-personal information to analyze user behavior and improve our website's functionality.

                     3. Data Security:
                     Fund Collection employs industry-standard security measures to protect your personal data. We use encryption, access controls, and regular security assessments to safeguard your information.

                     4. Sharing of Information:
                     We do not sell or rent your personal information to third parties. However, we may share information with trusted service providers who help us operate our platform or process payments on our behalf.

                     5. Cookies and Tracking:
                     We use cookies and similar tracking technologies to enhance your experience on our website. You can manage cookie preferences through your browser settings.

                     6. Your Choices:
                     You have the right to access, correct, or delete your personal information. You can also opt out of marketing communications. Please contact us to exercise these rights.

                     7. Changes to Privacy Policy:
                     Fund Collection may update this Privacy Policy from time to time. We will notify users of any significant changes via email or on our website.

                     Your privacy is of utmost importance to us. We are dedicated to providing a safe and secure platform for fundraising and donations, and we will always strive to protect your personal information as you entrust it to us. If you have any questions or concerns about our privacy practices, please contact us."
                  ],
                  "es" => [
                        "details" => "Política de privacidad para la recaudación de fondos

                        En Fund Collection, estamos comprometidos a salvaguardar su privacidad y garantizar la seguridad de su información personal. Esta Política de Privacidad describe nuestras prácticas relativas a la recopilación, el uso y la protección de los datos que nos proporciona al utilizar nuestra plataforma.

                        1. Información que recopilamos:
                        Podemos recopilar información personal, como su nombre, datos de contacto e información de pago, cuando realiza una donación o configura una campaña de recaudación de fondos en nuestra plataforma. También recopilamos información no personal, como su dirección IP y comportamiento de navegación, para mejorar nuestros servicios.

                        2. Uso de la Información:
                        Utilizamos su información personal para procesar donaciones, proporcionarle recibos y facilitar la comunicación con los organizadores de la campaña o los donantes. También podemos utilizar información no personal para analizar el comportamiento del usuario y mejorar la funcionalidad de nuestro sitio web.

                        3. Seguridad de los datos:
                        Fund Collection emplea medidas de seguridad estándar de la industria para proteger sus datos personales. Utilizamos cifrado, controles de acceso y evaluaciones de seguridad periódicas para salvaguardar su información.

                        4. Intercambio de información:
                        No vendemos ni alquilamos su información personal a terceros. Sin embargo, podemos compartir información con proveedores de servicios confiables que nos ayudan a operar nuestra plataforma o procesar pagos en nuestro nombre.

                        5. Cookies y seguimiento:
                        Utilizamos cookies y tecnologías de seguimiento similares para mejorar su experiencia en nuestro sitio web. Puede administrar las preferencias de cookies a través de la configuración de su navegador.

                        6. Tus opciones:
                        Tiene derecho a acceder, corregir o eliminar su información personal. También puede optar por no recibir comunicaciones de marketing. Por favor contáctenos para ejercer estos derechos.

                        7. Cambios a la Política de Privacidad:
                        Fund Collection puede actualizar esta Política de Privacidad de vez en cuando. Notificaremos a los usuarios sobre cualquier cambio significativo por correo electrónico o en nuestro sitio web.

                        Su privacidad es de suma importancia para nosotros. Nos dedicamos a proporcionar una plataforma segura para la recaudación de fondos y donaciones, y siempre nos esforzaremos por proteger su información personal cuando nos la confíe. Si tiene alguna pregunta o inquietud sobre nuestras prácticas de privacidad, comuníquese con nosotros."
                  ],
                  'ar' => [
                    "details" => "سياسة الخصوصية لجمع الأموال

                    في Fund Collection، نحن ملتزمون بحماية خصوصيتك وضمان أمان معلوماتك الشخصية. توضح سياسة الخصوصية هذه ممارساتنا المتعلقة بجمع واستخدام وحماية البيانات التي تقدمها لنا عند استخدام منصتنا.

                    1. المعلومات التي نجمعها:
                    قد نقوم بجمع معلومات شخصية، مثل اسمك وتفاصيل الاتصال بك ومعلومات الدفع، عندما تقوم بالتبرع أو إعداد حملة لجمع التبرعات على منصتنا. نقوم أيضًا بجمع معلومات غير شخصية، مثل عنوان IP الخاص بك وسلوك التصفح، لتحسين خدماتنا.

                    2. استخدام المعلومات:
                    نستخدم معلوماتك الشخصية لمعالجة التبرعات وتزويدك بالإيصالات وتسهيل التواصل مع منظمي الحملة أو الجهات المانحة. قد نستخدم أيضًا المعلومات غير الشخصية لتحليل سلوك المستخدم وتحسين وظائف موقعنا.

                    3. أمن البيانات:
                    تستخدم Fund Collection إجراءات أمنية متوافقة مع معايير الصناعة لحماية بياناتك الشخصية. نحن نستخدم التشفير وضوابط الوصول والتقييمات الأمنية المنتظمة لحماية معلوماتك.

                    4. تبادل المعلومات:
                    نحن لا نبيع أو نؤجر معلوماتك الشخصية لأطراف ثالثة. ومع ذلك، قد نشارك المعلومات مع مقدمي الخدمات الموثوقين الذين يساعدوننا في تشغيل منصتنا أو معالجة المدفوعات نيابة عنا.

                    5. ملفات تعريف الارتباط والتتبع:
                    نحن نستخدم ملفات تعريف الارتباط وتقنيات التتبع المماثلة لتعزيز تجربتك على موقعنا. يمكنك إدارة تفضيلات ملفات تعريف الارتباط من خلال إعدادات المتصفح الخاص بك.

                    6. اختياراتك:
                    لديك الحق في الوصول إلى معلوماتك الشخصية أو تصحيحها أو حذفها. يمكنك أيضًا إلغاء الاشتراك في الاتصالات التسويقية. يرجى الاتصال بنا لممارسة هذه الحقوق.

                    7. التغييرات في سياسة الخصوصية:
                    يجوز لجمع الأموال تحديث سياسة الخصوصية هذه من وقت لآخر. سنقوم بإخطار المستخدمين بأي تغييرات مهمة عبر البريد الإلكتروني أو على موقعنا.

                    خصوصيتك أمر في غاية الأهمية بالنسبة لنا. نحن ملتزمون بتوفير منصة آمنة لجمع التبرعات والتبرعات، وسنسعى دائمًا لحماية معلوماتك الشخصية عندما تعهد بها إلينا. إذا كان لديك أي أسئلة أو مخاوف بشأن ممارسات الخصوصية لدينا، يرجى الاتصال بنا."
                  ],
               ]
        ];
        $title  = [
            "language" => [
                  "en" => [
                     "title" => "Privacy  Policy"
                  ],
                  "es" => [
                        "title" => "Política de privacidad"
                  ],
                  "ar" => [
                    "title" => "سياسة الخصوصية"
                  ],
               ]
         ];
        SetupPage::create([
                'type'    => 'useful-links',
                'slug'    => 'privacy-policy',
                'title'   => $title,
                'url'     => null,
                'details' => $details,
                'last_edit_by' => 1,
                'status' => true,
            ]
        );

    }
}
