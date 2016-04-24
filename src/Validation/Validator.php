<?php
namespace Jihe\Validation;

use Illuminate\Validation\Factory;

class Validator
{
    /**
     * @var \Illuminate\Validation\Factory
     */
    private $validator;

    public function __construct(Factory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * validate
     *
     * @param mixed $request   request to be validated
     * @throws \Jihe\Exceptions\ValidationException    if validation fails, an instance of ValidationException
     *                                                     should be thrown
     */
    public function validate($request)
    {
        $validator = $this->validator->make(
            $request,
            $this->getRules(),
            $this->getMessages());

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }
    }

    /**
     * get the validation rules
     * override by the sub class to provide the validation rules
     *
     * @return array
     */
    protected function getRules()
    {
        return [];
    }

    /**
     * get the validation error messages
     * override by the sub class to provide the validation error messages
     *
     * @return array
     */
    protected function getMessages()
    {
        return [];
    }
}