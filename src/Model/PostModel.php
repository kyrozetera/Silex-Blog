<?php
namespace Blog\Model;

/* 
 * model to handle fetch & creation of posts
 */

class PostModel extends Model
{
    protected $fetchQuery = "SELECT p.id, p.subject, p.body, p.created, p.user_id, u.username FROM posts p
                             INNER JOIN users u ON u.id = p.user_id";
    
    private $id;
    public $subject;
    public $body;
    public $created;
    public $user_id;
    public $username;
    
    function __construct($conn)
    {
        parent::__construct($conn);
        $this->tableName = 'posts';
    }

    /**
     * 
     * @return array list of all posts
     */
    public function fetchAll()
    {
        return $this->conn->fetchAll($this->fetchQuery);
    }

    /**
     * load post by db identifier
     * @param int $id
     */
    public function getById($id)
    {
        $data = $this->conn->fetchArray($this->fetchQuery.' WHERE id = ?',array($id));
        $this->setData($data);
    }
    /**
     * saves new post to db / or updates current post
     * @param bool load whether to load newly inserted record into model
     * 
     * @return int inserted id
     */
    public function save($load=false)
    {
        $fields = array(
            'subject' => $this->subject,
            'body' => $this->body,
            'created' => $this->created?$this->created:date('Y-m-d h:i:s a'),
            'user_id' => $this->user_id
        );
            
        if($this->id == null)
        {
            return $this->id = $this->insert($fields,$load);
        }
        else
        {
            $fields['id'] = $this->id;
            return $this->update('posts', $fields);
        }
            
    }
    /**
     * 
     * @return int id of loaded or newly inserted record
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * sets model properties via array of key=>value pairs
     * 
     * @param array $data
     */
    public function setData($data)
    {
        $this->id = $data['id'];
        $this->subject = $data['subject'];
        $this->body = $data['body'];
        $this->created = $data['created'];
        $this->user_id = $data['user_id'];
        $this->username = $data['username'];
    }
}
