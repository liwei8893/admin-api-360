<?php

declare(strict_types=1);

namespace PHPSTORM_META {
    // Reflect
    override(\Psr\Container\ContainerInterface::get(0), map('@'));
    override(\Hyperf\Context\Context::get(0), map('@'));
    override(\make(0), map('@'));
    override(\di(0), map('@'));
}

namespace Hyperf\Database\Model {
    class Builder
    {
        /**
         * @return Builder
         */
        public function userDataScope(?int $userid = null)
        {
        }

        public function platformDataScope(?string $platformField = 'platform'): Builder
        {
        }

        public function vipOrder(): Builder
        {
        }

        public function notVipOrder(): Builder
        {

        }

        public function isNotExpire(): Builder
        {
        }

        public function noDeleteOrder(): Builder
        {
        }

        public function normalOrder(): Builder
        {
        }
    }
}
