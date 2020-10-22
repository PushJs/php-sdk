<?php
/**
 * Created by PhpStorm.
 * User: daems
 * Date: 23-06-16
 * Time: 17:11
 */

namespace pushjs\Library\Querybuilder;

use pushjs\Exception\PhpunionplatformException;

class HttpQueryBuilder
{
    /**
     * @var string
     */
    private $modes = 'sdc';

    /**
     * HttpQueryBuilder constructor.
     */
    public function __construct()
    {
    }


    public function buildHttpQuery(string $mode, array $params, int $rid, string $sid = '')
    {
        if (strpos('sdc', $this->modes) === false) {
            throw new PhpunionplatformException('wrong mode');
        }

        $data = [
            'mode' => $mode,
            'rid' => $rid
        ];

        if (!empty($sid)) {
            $data['sid'] = $sid;
        }

        if (!empty($params)) {
            foreach ($params as $key => $param) {
                $data[$key] = $param;
            }
        }

        return http_build_query($data);
    }
}
