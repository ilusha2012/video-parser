<?php

namespace app\models;

use GuzzleHttp\Client;
use Yii;

/**
 * This is the model class for table "{{%videos}}".
 *
 * @property integer $id
 * @property integer $resource_type
 * @property string $title
 * @property string $description
 * @property string $image
 * @property string $video_id
 *
 * @property Resources $resourceType
 */
class Videos extends \yii\db\ActiveRecord
{

    public $videoURL;

    public $resource;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%videos}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['videoURL'], 'required'],
            [['videoURL'], 'url'],
            [['videoURL'], 'validateResource'],
            [['videoURL'], 'validateIdResource'],
            [['title', 'description', 'image', 'video_id'], 'string'],
            [['resource', 'resource_type', 'title', 'description', 'image', 'video_id'], 'safe']
        ];
    }

    /**
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateResource($attribute, $params, $validator)
    {
        if (!$this->getResource()) {
            $this->addError($attribute, 'This service is not supported.');
        }
    }

    /**
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateIdResource($attribute, $params, $validator)
    {
        if (!$this->getIdByUrl()) {
            $this->addError($attribute, 'We do not find id of video.');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resource_type' => 'Resource Type',
            'title' => 'Title',
            'description' => 'Description',
            'image' => 'Image',
            'video_id' => 'Video ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResourceType()
    {
        return $this->hasOne(Resources::className(), ['id' => 'resource_type']);
    }

    /**
     * @inheritdoc
     * @return VideosQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VideosQuery(get_called_class());
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        $client = new Client();
        // Send GET-request to page with video.
        $res = $client->request('GET', $this->videoURL);
        // Get data between body-tags.
        $body = $res->getBody();
        // Include phpQuery.
        $document = \phpQuery::newDocumentHTML($body);
        //Find and get title
        $this->title = $document->find($this->getResource()->title_selector)->text();
        $this->description = $document->find($this->getResource()->description_selector)->text();
        $this->image = $this->getThumb();

        return parent::save();
    }

    /**
     * @return mixed
     */
    public function getThumb()
    {
        return str_replace('{id}', $this->video_id, $this->getResource()->image_selector);
    }

    /**
     * @return Resources|array|bool|int
     */
    public function getResource()
    {
        if ($this->resource_type) {
            $resource = Resources::find()->where(['id' => $this->resource_type]);
        } else {
            $host = parse_url($this->videoURL, PHP_URL_HOST);
            $resource = Resources::find()->where(['LIKE', 'example_url', "{$host}"]);
        }

        if (!$resource->exists())
            return false;

        $this->resource = $resource->one();

        return $this->resource;
    }

    /**
     * @return string
     */
    public function getIdByUrl()
    {
        if (!$this->video_id) {
            if ($this->resource->id_parameter) {
                parse_str(parse_url($this->videoURL, PHP_URL_QUERY), $vars);
                if (isset($vars[$this->resource->id_parameter])) {
                    $this->video_id = (string)$vars[$this->resource->id_parameter];
                }
            } else {
                $this->video_id = (string)parse_url($this->videoURL, PHP_URL_PATH);
            }
        }

        return $this->video_id;
    }
}
