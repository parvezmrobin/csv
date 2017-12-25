<?php
/**
 * User: Parvez
 * Date: 12/25/2017
 * Time: 4:31 AM
 */

namespace Csv;


use Traversable;

class Csv implements \JsonSerializable, \ArrayAccess, \Serializable, \IteratorAggregate
{
    protected $rows;

    /**
     * Csv constructor.
     * @param string|array|null $text
     */
    function __construct($text = null)
    {
        $this->unserialize($text);
    }

    public function toJson(): string
    {
        $json = $this->toArray();

        return json_encode($json);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $len = count($this->rows);
        $entryCount = count($this->rows[0]);
        $arr = [];

        for ($i = 1; $i < $len; $i++) {
            $entry = [];
            for ($j = 0; $j < $entryCount; $j++) {
                $entry[$this->rows[0][$j]] = key_exists($j, $this->rows[$i]) ? $this->rows[$i][$j] : null;
            }
            $arr[] = $entry;
        }
        return $arr;
    }

    /**
     * @param string $filePath
     * @return int
     */
    public function toFile(string $filePath)
    {
        if (file_exists($filePath) && !is_writable($filePath)) {
            throw new \InvalidArgumentException("$filePath is not writable");
        }

        $handle = fopen($filePath, 'w');
        $ret = fwrite($handle, $this->__toString());
        fclose($handle);

        return $ret;
    }

    public function __toString()
    {
        $lines = array_map(
            function ($row) {
                return implode(",", $row);
            },
            $this->rows);

        return implode("\n", $lines);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return key_exists($offset, $this->rows);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return explode(",", $this->rows[$offset]);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $value = is_array($value) ? $value : explode(",", $value);
        $this->rows[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->rows[$offset]);
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return $this->__toString();
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $strings = is_array($serialized) ? $serialized : explode("\n", $serialized);

        $this->rows = array_map(
            function ($value) {
                return explode(",", $value);
            },
            $strings);
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        foreach ($this->rows as $index => $row) {
            yield $index => $row;
        }
    }
}