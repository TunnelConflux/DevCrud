<?php
/**
 * Project      : DevCrud
 * File Name    : InputTypes.php
 * Author       : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2019/07/09 1:18 PM
 */

namespace TunnelConflux\DevCrud\Models\Enums;

final class JoinTypes extends Enum
{
    const OneToOne = "oneToOne";
    const OneToMany = "oneToMany";
    const ManyToOne = "manyToOne";
    const ManyToMany = "manyToMany";
}
