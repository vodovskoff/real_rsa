<?php
require_once dirname(__DIR__).'/vendor/autoload.php';
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(dirname(__DIR__, 1).'/src/Template/');
$twig = new Environment($loader);
$path = explode('?', $_SERVER['REQUEST_URI']);
$uri = $path[0];

switch ($uri){
    case '/encrypt':
    {
        if(isset($_POST['message'])&&isset($_POST['e']) && isset($_POST['n']) && $_POST['e']!='' || $_POST['n']!=''){
                $encrypted = User\RealRsa\Service\RSA::encrypt($_POST['message'],$_POST['e'],
                    $_POST['n'],  );
                $value = "Защифрованное сообщение: $encrypted";
            }
        $template = $twig->load('/encrypt.html.twig');
        echo $template->render(['value'=>$value]);
        break;
    }
    case '/generate':
    {
        if (isset($_POST['submit'])) {
            if (!isset($_POST['p']) || !isset($_POST['q']) || $_POST['p'] == '' || $_POST['q'] == '') {
                $pq = \User\RealRsa\Service\RSA::generatePQ();
                $p = $pq[0];
                $q = $pq[1];
            } else {
                if (!User\RealRsa\Service\RSA::isPrime($_POST['p']) || !User\RealRsa\Service\RSA::isPrime($_POST['q']))
                {
                    $pq = User\RealRsa\Service\RSA::generatePQ();
                    $p = $pq[0];
                    $q = $pq[1];
                } else {
                    $p = $_POST['p'];
                    $q = $_POST['q'];
                }
            }
            $n = $p * $q;
            $m = ($p - 1) * ($q - 1);

            $e = User\RealRsa\Service\RSA::generateEncryptor($m);
            $d = User\RealRsa\Service\RSA::generateDecryptor($e, $m);

            $value = "P=$p, Q=$q, Ключ шифрования = ($e, $n), Ключ дешифрования = ($d, $n)";
        }
        $template = $twig->load('/generate.html.twig');
        echo $template->render(['value'=>$value]);
        break;
    }
    case '/decrypt':
    {
        if(isset($_POST['message'])&&isset($_POST['в']) && isset($_POST['n']) && $_POST['в']!='' || $_POST['n']!=''){
            $decrypted = User\RealRsa\Service\RSA::decrypt($_POST['message'],$_POST['d'], $_POST['n'],  );
            $value = "Расшифрованное сообщение: $decrypted";
        }
        $template = $twig->load('/decrypt.html.twig');
        echo $template->render(['value'=>$value]);
        break;
        break;
    }
    default:
    {
        $template = $twig->load('/index.html.twig');
        echo $template->render();
        break;
    }
}