<?php

namespace PartialDelivery\Model\Base;

use \Exception;
use \PDO;
use PartialDelivery\Model\OrderProductPartialDelivery as ChildOrderProductPartialDelivery;
use PartialDelivery\Model\OrderProductPartialDeliveryQuery as ChildOrderProductPartialDeliveryQuery;
use PartialDelivery\Model\Map\OrderProductPartialDeliveryTableMap;
use PartialDelivery\Model\Thelia\Model\OrderProduct;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'order_product_partial_delivery' table.
 *
 *
 *
 * @method     ChildOrderProductPartialDeliveryQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildOrderProductPartialDeliveryQuery orderBySentQuantity($order = Criteria::ASC) Order by the sent_quantity column
 *
 * @method     ChildOrderProductPartialDeliveryQuery groupById() Group by the id column
 * @method     ChildOrderProductPartialDeliveryQuery groupBySentQuantity() Group by the sent_quantity column
 *
 * @method     ChildOrderProductPartialDeliveryQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildOrderProductPartialDeliveryQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildOrderProductPartialDeliveryQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildOrderProductPartialDeliveryQuery leftJoinOrderProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrderProduct relation
 * @method     ChildOrderProductPartialDeliveryQuery rightJoinOrderProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrderProduct relation
 * @method     ChildOrderProductPartialDeliveryQuery innerJoinOrderProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the OrderProduct relation
 *
 * @method     ChildOrderProductPartialDelivery findOne(ConnectionInterface $con = null) Return the first ChildOrderProductPartialDelivery matching the query
 * @method     ChildOrderProductPartialDelivery findOneOrCreate(ConnectionInterface $con = null) Return the first ChildOrderProductPartialDelivery matching the query, or a new ChildOrderProductPartialDelivery object populated from the query conditions when no match is found
 *
 * @method     ChildOrderProductPartialDelivery findOneById(int $id) Return the first ChildOrderProductPartialDelivery filtered by the id column
 * @method     ChildOrderProductPartialDelivery findOneBySentQuantity(int $sent_quantity) Return the first ChildOrderProductPartialDelivery filtered by the sent_quantity column
 *
 * @method     array findById(int $id) Return ChildOrderProductPartialDelivery objects filtered by the id column
 * @method     array findBySentQuantity(int $sent_quantity) Return ChildOrderProductPartialDelivery objects filtered by the sent_quantity column
 *
 */
abstract class OrderProductPartialDeliveryQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \PartialDelivery\Model\Base\OrderProductPartialDeliveryQuery object.
     *
     * @param string $dbName     The database name
     * @param string $modelName  The phpName of a model, e.g. 'Book'
     * @param string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\PartialDelivery\\Model\\OrderProductPartialDelivery', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildOrderProductPartialDeliveryQuery object.
     *
     * @param string   $modelAlias The alias of a model in the query
     * @param Criteria $criteria   Optional Criteria to build the query from
     *
     * @return ChildOrderProductPartialDeliveryQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \PartialDelivery\Model\OrderProductPartialDeliveryQuery) {
            return $criteria;
        }
        $query = new \PartialDelivery\Model\OrderProductPartialDeliveryQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed               $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildOrderProductPartialDelivery|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = OrderProductPartialDeliveryTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(OrderProductPartialDeliveryTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param mixed               $key Primary key to use for the query
     * @param ConnectionInterface $con A connection object
     *
     * @return ChildOrderProductPartialDelivery A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, SENT_QUANTITY FROM order_product_partial_delivery WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildOrderProductPartialDelivery();
            $obj->hydrate($row);
            OrderProductPartialDeliveryTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param mixed               $key Primary key to use for the query
     * @param ConnectionInterface $con A connection object
     *
     * @return ChildOrderProductPartialDelivery|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param array               $keys Primary keys to use for the query
     * @param ConnectionInterface $con  an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param mixed $key Primary key to use for the query
     *
     * @return ChildOrderProductPartialDeliveryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        return $this->addUsingAlias(OrderProductPartialDeliveryTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param array $keys The list of primary key to use for the query
     *
     * @return ChildOrderProductPartialDeliveryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        return $this->addUsingAlias(OrderProductPartialDeliveryTableMap::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @see       filterByOrderProduct()
     *
     * @param mixed  $id         The value to use as filter.
     *                           Use scalar values for equality.
     *                           Use array values for in_array() equivalent.
     *                           Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductPartialDeliveryQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(OrderProductPartialDeliveryTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(OrderProductPartialDeliveryTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderProductPartialDeliveryTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the sent_quantity column
     *
     * Example usage:
     * <code>
     * $query->filterBySentQuantity(1234); // WHERE sent_quantity = 1234
     * $query->filterBySentQuantity(array(12, 34)); // WHERE sent_quantity IN (12, 34)
     * $query->filterBySentQuantity(array('min' => 12)); // WHERE sent_quantity > 12
     * </code>
     *
     * @param mixed  $sentQuantity The value to use as filter.
     *                             Use scalar values for equality.
     *                             Use array values for in_array() equivalent.
     *                             Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison   Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductPartialDeliveryQuery The current query, for fluid interface
     */
    public function filterBySentQuantity($sentQuantity = null, $comparison = null)
    {
        if (is_array($sentQuantity)) {
            $useMinMax = false;
            if (isset($sentQuantity['min'])) {
                $this->addUsingAlias(OrderProductPartialDeliveryTableMap::SENT_QUANTITY, $sentQuantity['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sentQuantity['max'])) {
                $this->addUsingAlias(OrderProductPartialDeliveryTableMap::SENT_QUANTITY, $sentQuantity['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderProductPartialDeliveryTableMap::SENT_QUANTITY, $sentQuantity, $comparison);
    }

    /**
     * Filter the query by a related \PartialDelivery\Model\Thelia\Model\OrderProduct object
     *
     * @param \PartialDelivery\Model\Thelia\Model\OrderProduct|ObjectCollection $orderProduct The related object(s) to use as filter
     * @param string                                                            $comparison   Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductPartialDeliveryQuery The current query, for fluid interface
     */
    public function filterByOrderProduct($orderProduct, $comparison = null)
    {
        if ($orderProduct instanceof \PartialDelivery\Model\Thelia\Model\OrderProduct) {
            return $this
                ->addUsingAlias(OrderProductPartialDeliveryTableMap::ID, $orderProduct->getId(), $comparison);
        } elseif ($orderProduct instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrderProductPartialDeliveryTableMap::ID, $orderProduct->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByOrderProduct() only accepts arguments of type \PartialDelivery\Model\Thelia\Model\OrderProduct or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrderProduct relation
     *
     * @param string $relationAlias optional alias for the relation
     * @param string $joinType      Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildOrderProductPartialDeliveryQuery The current query, for fluid interface
     */
    public function joinOrderProduct($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrderProduct');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'OrderProduct');
        }

        return $this;
    }

    /**
     * Use the OrderProduct relation OrderProduct object
     *
     * @see useQuery()
     *
     * @param string $relationAlias optional alias for the relation,
     *                              to be used as main alias in the secondary query
     * @param string $joinType      Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \PartialDelivery\Model\Thelia\Model\OrderProductQuery A secondary query class using the current class as primary query
     */
    public function useOrderProductQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrderProduct($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrderProduct', '\PartialDelivery\Model\Thelia\Model\OrderProductQuery');
    }

    /**
     * Exclude object from result
     *
     * @param ChildOrderProductPartialDelivery $orderProductPartialDelivery Object to remove from the list of results
     *
     * @return ChildOrderProductPartialDeliveryQuery The current query, for fluid interface
     */
    public function prune($orderProductPartialDelivery = null)
    {
        if ($orderProductPartialDelivery) {
            $this->addUsingAlias(OrderProductPartialDeliveryTableMap::ID, $orderProductPartialDelivery->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the order_product_partial_delivery table.
     *
     * @param  ConnectionInterface $con the connection to use
     * @return int                 The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderProductPartialDeliveryTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            OrderProductPartialDeliveryTableMap::clearInstancePool();
            OrderProductPartialDeliveryTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildOrderProductPartialDelivery or Criteria object OR a primary key value.
     *
     * @param  mixed               $values Criteria or ChildOrderProductPartialDelivery object or primary key or array of primary keys
     *                                     which is used to create the DELETE statement
     * @param  ConnectionInterface $con    the connection to use
     * @return int                 The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                                    if supported by native driver or if emulated using Propel.
     * @throws PropelException     Any exceptions caught during processing will be
     *                                    rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderProductPartialDeliveryTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(OrderProductPartialDeliveryTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

        OrderProductPartialDeliveryTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            OrderProductPartialDeliveryTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // OrderProductPartialDeliveryQuery
