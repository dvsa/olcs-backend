<?php
/**
 * Bus Reg List View
 *
 * @NOTE: This walks and talks like an entity but be warned, it is backed
 * by a view. As such it is is nicely readable and searchable, but writes
 * are a no go.
 *
 * You'll notice that the entity has no setters; this is intentional to
 * try and prevent accidental writes. It's marked as readOnly too to
 * prevent doctrine including it in any flushes
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Olcs\Db\Entity\View;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Interfaces;

/**
 * Bus Reg List View
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="bus_reg_list_view")
 */
class BusRegListView implements Interfaces\EntityInterface
{
    //
}
