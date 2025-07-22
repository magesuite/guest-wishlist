<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Model\ResourceModel;

class Wishlist extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('wishlist', 'wishlist_id');
    }

    public function removeOlderThan(int $retentionPeriodInDays, bool $emptyOnly = true): void
    {
        $whereCond = [
            sprintf('updated_at < date_sub(CURDATE(), INTERVAL %s Day)', $retentionPeriodInDays),
            'customer_id = 0'
        ];

        if ($emptyOnly) {
            $whereCond[] = new \Zend_Db_Expr(
                sprintf('wishlist_id NOT IN (SELECT wishlist_id FROM %s)', $this->getTable('wishlist_item'))
            );
        }

        $this->getConnection()->delete($this->getMainTable(), $whereCond);
    }
}
