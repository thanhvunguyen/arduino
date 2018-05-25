<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once SHAREDPATH . "libraries/Aws/APP_Aws.php";

/**
 * AWS CloudSearchクラス
 *
 * @version $id$
 */
class APP_Aws_cloudfront extends APP_Aws
{
    /**
     * インスタンス名を取得
     *
     * @access public
     * @return string
     */
    public function instance_name()
    {
        return "CloudFront";
    }

    public function create_invalidation($distribution_id, $path)
    {
        return $this->instance()->createInvalidation([
            'DistributionId' => $distribution_id,
            'Paths' => [
                'Quantity' => count($path),
                'Items' => $path
            ],
            'CallerReference' => time()
        ]);
    }

}
