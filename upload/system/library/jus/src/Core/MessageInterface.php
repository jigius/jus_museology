<?php
namespace Jus\Core;

interface MessageInterface
{
	const CONTAINER_NAME = "msg";

	const TYPE_SUCCESS = 0;
	const TYPE_WARNING = 1;
	const TYPE_ERROR = 2;
}
