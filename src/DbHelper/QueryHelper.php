<?php
declare(strict_types=1);

namespace Mzh\Helper\DbHelper;


use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;

/**
 * 搜索条件处理器
 * Class QueryHelper
 */
class QueryHelper
{
    /**
     * 数据库实例
     * @var Builder
     */
    public $query;
    private $data;

    /**
     * Query call.
     * @param string $name 调用方法名称
     * @param array $args 调用参数内容
     * @return $this
     */
    public function __call($name, $args)
    {
        if (is_callable($callable = [$this->query, $name])) {
            call_user_func_array($callable, $args);
        }
        return $this;
    }

    /**
     * 逻辑器初始化
     * @param string|Builder $dbQuery
     * @return $this
     */
    public function setQuery($dbQuery)
    {
        $this->query = $dbQuery;
        return $this;
    }

    /**
     * 逻辑器初始化
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 追加数据
     * @param array $data
     * @return $this
     */
    public function addData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
            return $this;
        }
        if ($value === null) {
            unset($this->data[$key]);
            return;
        }
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * 获取当前Db操作对象
     * @return Builder
     */
    public function db()
    {
        return $this->query;
    }

    /**
     * 设置Like查询条件
     * @param string|array $fields 查询字段
     * @param string $alias 别名分割符
     * @return $this
     */
    public function like($fields, $alias = '#', $boolean = 'and')
    {
        $data = $this->data;
        foreach (is_array($fields) ? $fields : explode(',', $fields) as $field) {
            list($dk, $qk) = [$field, $field];
            if (stripos($field, $alias) !== false) {
                list($dk, $qk) = explode($alias, $field);
            }
            if (isset($data[$qk]) && $data[$qk] !== '') {
                $this->query = $this->query->where($dk, 'like', "%{$data[$qk]}%", $boolean);
            }
        }
        return $this;
    }

    /**
     * 设置手动设置查询条件
     * @param string|array $fields 查询字段
     * @param string $alias 别名分割符
     * @return $this
     */
    public function where($fields, $val)
    {
        $this->query = $this->query->where($fields, $val);
        return $this;
    }

    /**
     * 设置Equal查询条件
     * @param string|array $fields 查询字段
     * @param string $alias 别名分割符
     * @return $this
     */
    public function equal($fields, $alias = '#')
    {
        $data = $this->data;
        foreach (is_array($fields) ? $fields : explode(',', $fields) as $field) {
            list($dk, $qk) = [$field, $field];
            if (stripos($field, $alias) !== false) {
                list($dk, $qk) = explode($alias, $field);
            }
            if (isset($data[$qk]) && $data[$qk] !== '') {
                $this->query = $this->query->where($dk, "{$data[$qk]}");
            }
        }
        return $this;
    }

    /**
     * 设置IN区间查询
     * @param string $fields 查询字段
     * @param string $split 输入分隔符
     * @param string $alias 别名分割符
     * @return $this
     */
    public function in($fields, $split = ',', $alias = '#')
    {
        $data = $this->data;
        foreach (is_array($fields) ? $fields : explode(',', $fields) as $field) {
            list($dk, $qk) = [$field, $field];
            if (stripos($field, $alias) !== false) {
                list($dk, $qk) = explode($alias, $field);
            }
            if (isset($data[$qk]) && $data[$qk] !== '') {
                $this->query = $this->query->whereIn($dk, explode($split, $data[$qk]));
            }
        }
        return $this;
    }

    /**
     * 设置内容区间查询
     * @param string|array $fields 查询字段
     * @param string $split 输入分隔符
     * @param string $alias 别名分割符
     * @return $this
     */
    public function valueBetween($fields, $split = ' ', $alias = '#')
    {
        return $this->setBetweenWhere($fields, $split, $alias);
    }

    /**
     * 设置日期时间区间查询
     * @param string|array $fields 查询字段
     * @param string $split 输入分隔符
     * @param string $alias 别名分割符
     * @return $this
     */
    public function dateBetween($fields, $alias = '#', $split = ' - ')
    {
        return $this->setBetweenWhere($fields, $split, $alias, function ($value, $type) {
            if ($type === 'after') {
                return "{$value} 23:59:59";
            } else {
                return "{$value} 00:00:00";
            }
        });
    }

    /**
     * 设置时间戳区间查询
     * @param string|array $fields 查询字段
     * @param string $split 输入分隔符
     * @param string $alias 别名分割符
     * @return $this
     */
    public function timeBetween($fields, $alias = '#', $split = ' - ')
    {
        return $this->setBetweenWhere($fields, $split, $alias, function ($value, $type) {
            if ($type === 'after') {
                return strtotime("{$value} 23:59:59");
            } else {
                return strtotime("{$value} 00:00:00");
            }
        });
    }

    /**
     * 设置区域查询条件
     * @param string|array $fields 查询字段
     * @param string $split 输入分隔符
     * @param string $alias 别名分割符
     * @param callable $callback
     * @return $this
     */
    private function setBetweenWhere($fields, $split = ' ', $alias = '#', $callback = null)
    {
        $data = $this->data;
        foreach (is_array($fields) ? $fields : explode(',', $fields) as $field) {
            list($dk, $qk) = [$field, $field];
            if (stripos($field, $alias) !== false) {
                list($dk, $qk) = explode($alias, $field);
            }
            if (isset($data[$qk]) && $data[$qk] !== '') {
                if (is_array($data[$qk])) {
                    list($begin, $after) = $data[$qk];
                } else {
                    list($begin, $after) = explode($split, $data[$qk]);
                }
                if (is_callable($callback)) {
                    $after = call_user_func($callback, $after, 'after');
                    $begin = call_user_func($callback, $begin, 'begin');
                }
                $this->query = $this->query->whereBetween($dk, [$begin, $after]);
            }
        }
        return $this;
    }

    /**
     * 实例化分页管理器
     * @param array $columns
     * @param null $callback
     * @return mixed
     */
    public function paginate($columns = ['*'], $callback = null)
    {
        $limit = (isset($this->data['limit']) && $this->data['limit'] > 0) ? $this->data['limit'] : 20;
        $page = (isset($this->data['page'])) ? $this->data['page'] : 1;
        $offset = ($page - 1) * $limit;
        $new = clone $this->query;
        $db = $this->query->offset($offset)->limit($limit);
        if (isset($this->data['order_field'])) {
            $db->orderBy($this->data['order_field'], $this->data['order_type'] ?? 'desc');
        } else {
            $db = $db->latest();
        }
        $list = $db->get($columns);
        /** @var Collection|static[] $list */
        if ($list->isEmpty()) {
            return [
                'items' => [],
                'page' => 0,
                'has_more' => false,
                'total_result' => 0,
            ];
        }
        if (is_callable($callback)) {
            $list = call_user_func($callback, $list);
        }
        return [
            'items' => $list->toArray(),
            'page' => $page ?? 0,
            'has_more' => $list->count() == ($limit ?? 20),
            'total_result' => $new->count(),
        ];
    }

    /**
     * 获取所有结果
     * @param string[] $columns
     * @param null $callback
     * @return mixed
     */
    public function get($columns = ['*'], $callback = null)
    {
        if (isset($this->data['order_field'])) {
            $this->query->orderBy($this->data['order_field'], $this->data['order_type'] ?? 'desc');
        } else {
            $this->query->latest();
        }
        $list = $this->query->get($columns);
        if (is_callable($callback)) {
            $list = call_user_func($callback, $list);
        }
        return $list;
    }
}