<?php

namespace robuust\laposta\controllers;

use Craft;
use craft\web\Controller;
use craft\web\Response;
use Laposta_Error;
use Laposta_Member;

/**
 * Submit controller.
 */
class SubmitController extends Controller
{
    /**
     * {@inheritdoc}
     */
    protected array|bool|int $allowAnonymous = true;

    /**
     * Submit form to Laposta.
     *
     * @return Response|null
     */
    public function actionIndex(): ?Response
    {
        $this->requirePostRequest();

        $values = $this->request->getBodyParams();
        $member = new Laposta_Member($values['list_id']);

        $errors = null;
        try {
            $result = $member->create([
                'ip' => $this->request->getRemoteIP(),
                'email' => $values['email'],
                'source_url' => $this->request->getReferrer(),
                'custom_fields' => $values,
            ]);
        } catch (Laposta_Error $e) {
            $errors = $e->getJsonBody()['error'];
        }

        if ($errors) {
            if ($this->request->getAcceptsJson()) {
                return $this->asJson([
                    'errors' => $errors,
                ]);
            }

            // Send the entry back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'errors' => $errors,
            ]);

            return null;
        }

        if ($this->request->getAcceptsJson()) {
            return $this->asJson($result);
        }

        return $this->redirectToPostedUrl((object) $result);
    }
}
