<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Class Apple
 * @property int $id
 * @property string $color
 * @property int $created_at
 * @property int|null $fell_at
 * @property string $status
 * @property float $eaten_percent
 * @property int|null $updated_at
 */
class Apple extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%apples}}';
    }

    public function rules()
    {
        return [
            [['color'], 'required'],
            [['created_at', 'fell_at', 'updated_at'], 'integer'],
            [['eaten_percent'], 'number', 'min' => 0, 'max' => 100],
            [['status'], 'in', 'range' => ['on_tree', 'on_ground', 'rotten']],
            [['color'], 'string', 'max' => 20],
        ];
    }

    public function behaviors()
    {
        return [
            // автоматическое обновление updated_at при сохранении (не обязательно created_at — задаём вручную)
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => false,
                'updatedAtAttribute' => 'updated_at',
                'value' => function() { return time(); },
            ],
        ];
    }

    /**
     * Создать яблоко с случайным цветом и случайным created_at (в пределах последних 24 часов)
     * @param string|null $color
     * @return static
     */
    public static function createRandom($color = null)
    {
        $apple = new self();
        $apple->color = $color ?: self::randomColor();
        $apple->created_at = rand(time() - 24*3600, time());
        $apple->status = 'on_tree';
        $apple->eaten_percent = 0;
        $apple->save(false);
        return $apple;
    }

    public static function randomColor()
    {
        $colors = ['green', 'red', 'yellow'];
        return $colors[array_rand($colors)];
    }

    /**
     * @return float Размер яблока от 0 до 1
     */
    public function getSize(): float
    {
        return max(0.0, 1.0 - ($this->eaten_percent / 100.0));
    }

    /**
     * Обновить статус (проверить, не испортилось ли)
     * Если яблоко упало и прошло >= 5 часов — статус => rotten
     * Вызывать перед операциями, а также при загрузке списка (например в контроллере)
     */
    public function refreshStatus()
    {
        if ($this->status === 'on_ground' && $this->fell_at) {
            if (time() - $this->fell_at >= 5 * 3600) {
                $this->status = 'rotten';
                $this->save(false);
            }
        }
    }

    /**
     * Упасть на землю
     * @throws \DomainException
     */
    public function fall()
    {
        if ($this->status !== 'on_tree') {
            throw new \DomainException('Яблоко уже не висит на дереве.');
        }
        $this->status = 'on_ground';
        $this->fell_at = time();
        $this->save(false);
    }

    /**
     * Съесть процент яблока
     * @param float $percent
     * @throws \DomainException
     * @return bool|string Возвращает 'deleted' если яблоко удалено после поедания
     */
    public function eat(float $percent)
    {
        // обновим статус (в т.ч. переведём в rotten, если прошло время)
        $this->refreshStatus();

        if ($this->status === 'on_tree') {
            throw new \DomainException('Съесть нельзя — яблоко на дереве.');
        }
        if ($this->status === 'rotten') {
            throw new \DomainException('Съесть нельзя — яблоко испорчено.');
        }
        if ($percent <= 0) {
            throw new \DomainException('Процент должен быть положительным.');
        }

        $this->eaten_percent += $percent;
        if ($this->eaten_percent >= 100.0) {
            // полностью съедено — удаляем
            $this->delete();
            return 'deleted';
        }

        $this->save(false);
        return true;
    }
}
