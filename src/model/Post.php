<?php 
namespace m2i\app\model;
use m2i\framework\EntityInterface;
use \DateTime;
use m2i\app\model\User;


class Post implements EntityInterface{

private ?int $id = null;
private string $title;
private string $content;
private DateTime $createdAt;
private User $user;
private ?int $parentId;
private array $answers = [];

public function __construct(){
    $this->createdAt = new DateTime();
 
}

/**
 * Get the value of id
 *
 * @return ?int
 */
public function getId(): ?int
{
return $this->id;
}

/**
 * Set the value of id
 *
 * @param ?int $id
 *
 * @return self
 */
public function setId(?int $id): self
{
$this->id = $id;

return $this;
}

/**
 * Get the value of title
 *
 * @return string
 */
public function getTitle(): string
{
return $this->title;
}

/**
 * Set the value of title
 *
 * @param string $title
 *
 * @return self
 */
public function setTitle(string $title): self
{
$this->title = $title;

return $this;
}


/**
 * Get the value of user
 *
 * @return User
 */
public function getUser(): User
{
return $this->user;
}

/**
 * Set the value of user
 *
 * @param User $user
 *
 * @return self
 */
public function setUser(User $user): self
{
$this->user = $user;

return $this;
}

/**
 * Get the value of parent
 *
 * @return Post
 */
public function getParentId(): int
{
return $this->parentId;
}

/**
 * Set the value of parent
 *
 * @param int $parent
 *
 * @return self
 */
public function setParentId(?int $parent): self
{
$this->parentId = $parent;

return $this;
}

/**
 * Get the value of answers
 *
 * @return array
 */
public function fetchAnswers(): array
{
return $this->answers;
}

/**
 * Set the value of answers
 *
 * @param array $answers
 *
 * @return self
 */
public function setAnswers(array $answers): self
{
$this->answers = $answers;

return $this;
}

/**
 * Get the value of content
 *
 * @return string
 */
public function getContent(): string
{
return $this->content;
}

/**
 * Set the value of content
 *
 * @param string $content
 *
 * @return self
 */
public function setContent(string $content): self
{
$this->content = $content;

return $this;
}

/**
 * Get the value of createdAt
 *
 * @return DateTime
 */
public function getCreatedAt(): DateTime
{
return $this->createdAt;
}

/**
 * Set the value of createdAt
 *
 * @param string $createdAt
 *
 * @return self
 */
public function setCreatedAt(string $createdAt): self
{
$this->createdAt = new DateTime($createdAt);

return $this;
}
}

?>