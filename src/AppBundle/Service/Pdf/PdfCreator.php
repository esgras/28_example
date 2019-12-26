<?php

namespace AppBundle\Service\Pdf;

class PdfCreator
{
    public function createOrder($card, $date, $orderNumber, $expired, $owner, $amount, $code)
    {
        ob_start();
        $img_path = __DIR__ . '/assets/bank.png';

        include 'template/order.html';
        $content = ob_get_clean();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}