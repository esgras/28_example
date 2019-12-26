<?php

namespace AppBundle\Admin\Controller;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\Config;
use AppBundle\Helper\ConstantHelper;
use Symfony\Component\HttpFoundation\Request;

class SettingsController extends BaseController
{

    public function mainAction(Request $request)
    {
        $view = '@EasyAdmin/settings/main.html.twig';
        $configs = $this->em->getRepository(Config::class)->findBy(['type' => Config::TYPE_MAIN]);
        $configsName = [];
        $params = [];

        foreach ($configs as $key => $conf) {
            $configsName[$key] = $conf->getName();
        }

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();

            foreach ($data as $key => $val) {
                if (($keyIndex = array_search($key, $configsName)) !== false) {
                    $conf = $configs[$keyIndex];
                    $conf->setValue($val);
                }
            }
            $this->em->flush();

            return $this->redirect($request->getUri());
        }

        foreach ($configs as $config) {
            $params[strtolower($config->getName())] = $config;
        }

        return $this->render($view, [
            'elems' => $params
        ]);
    }

    public function discountMailAction(Request $request)
    {
        $view = '@EasyAdmin/settings/discount_mail.html.twig';
        $configs = $this->em->getRepository(Config::class)->getDiscountConfigs();
        $configsName = [];
        $params = [];

        foreach ($configs as $key => $conf) {
            $configsName[$key] = $conf->getName();
        }
        
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $keys = array_keys($data);

            foreach ($data as $key => $val) {
                if (($keyIndex = array_search($key, $configsName)) !== false) {
                    $val = $val < 0 ? 0 : $val;
                    $configs[$keyIndex]->setValue($val);
                }
            }

            foreach (array_diff($configsName, $keys) as $key) {
                if (($keyIndex = array_search($key, $configsName)) !== false) {
                    $configs[$keyIndex]->setValue(NULL);
                }
            }

            $this->em->flush();

            return $this->redirect($request->getUri());
        }

        foreach ($configs as $config) {
            $params[strtolower($config->getName())] = $config;
        }

        return $this->render($view, [
            'elems' => $params,
            'discountStatusKey' => 'discount_status'
        ]);
    }


}