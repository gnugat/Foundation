<?php
/*
 * This file is part of PommProject's Foundation package.
 *
 * (c) 2014 Grégoire HUBERT <hubert.greg@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PommProject\Foundation\Converter\Geometry;

use PommProject\Foundation\Session;
use PommProject\Foundation\Converter\ConverterInterface;
use PommProject\Foundation\Converter\Type\Point;
use PommProject\Foundation\Exception\ConverterException;

class PgPoint implements ConverterInterface
{
    protected $type_class_name;

    public function __construct($type_class_name = null)
    {
        $this->type_class_name = $type_class_name === null
            ? '\PommProject\Foundation\Converter\Type\Point'
            : $type_class_name
            ;
    }

    /**
     * fromPg
     *
     * @see ConverterInterface
     */
    public function fromPg($data, $type, Session $session)
    {
        $data = trim($data, ' ()');

        if (!preg_match('/([0-9e\-+\.]+),([0-9e\-+\.]+)/', $data, $matchs)) {
            if ($data === '') {
                return null;
            }

            throw new ConverterException(
                sprintf(
                    "Could not parse point representation '%s'.",
                    $data
                )
            );
        }

        $class = $this->type_class_name;

        return new $class((float) $matchs[1], (float) $matchs[2]);
    }

    /**
     * toPg
     *
     * @see ConverterInterface
     */
    public function toPg($data, $type, Session $session)
    {
        if ($data === null) {
            return sprintf("NULL::%s", $type);
        }

        if (!$data instanceOf Point) {
            $data = $this->fromPg($data, $type, $session);
        }

        return sprintf("point(%s,%s)", $data->x, $data->y);
    }
}