<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute may only contain letters.',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute may only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'date' => 'The :attribute is not a valid date.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => 'The :attribute must be a valid email address.',
    'exists' => 'The selected :attribute is invalid.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => 'The :attribute may not be greater than :max characters.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => 'The :attribute must be a number.',
    'present' => 'The :attribute field must be present.',
    'regex' => 'The :attribute format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute format is invalid.',
    'uuid' => 'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name'                  => 'Name',
        'username'              => 'Username',
        'nickname'              => 'Nickname',
        'email'                 => 'Email Address',
        'first_name'            => 'First Name',
        'last_name'             => 'Last Name',
        "id_number"             => 'ID Number',
        'password'              => 'Password',
        'birth_date'            => 'Birth Date',
        'agreement'             => 'Agreement',
        'password_confirmation' => 'Password Confirmation',
        'city'                  => 'City',
        'district'              => 'District',
        'street'                => 'Street',
        'country'               => 'Country',
        'address'               => 'Address',
        'phone'                 => 'Phone',
        'mobile'                => 'Mobile',
        'age'                   => 'Age',
        'sex'                   => 'Sex',
        'gender'                => 'Gender',
        'day'                   => 'Day',
        'month'                 => 'Month',
        'year'                  => 'Year',
        'hour'                  => 'Hour',
        'minute'                => 'Minute',
        'second'                => 'Second',
        'title'                 => 'Title',
        'content'               => 'Content',
        'description'           => 'Description',
        'excerpt'               => 'Excerpt',
        'date'                  => 'Date',
        'time'                  => 'Time',
        'available'             => 'Available',
        'size'                  => 'Size',
        'activation_code'       => 'Activation Code',
        'longitude'             => 'Longitude',
        'latitude'              => 'Latitude',
        'day_date'              => 'Day Date',
        'from_time'             => 'From Time',
        'to_time'               => 'To Time',
        'doctor_id'             => 'Doctor ID',
        'payment_method_id'     => 'Payment Method ID',
        'use_insurance'         => 'Use Insurance',
        'paid'                  => 'Paid',
        'rate'                  => 'Rate',
        'importance'            => 'Importance',
        'type'                  => 'Type',
        'message'               => 'Message',
        'reservation_no'        => 'Reservation Number',
        'reason'                => 'Reason',
        'branch_no'             => 'Branch Number',
        'name_en'               => 'Name in English',
        'name_ar'               => 'Name in Arabic',
        'city_id'               => 'City id',
        'distinct_id'           => 'Distinct id',
        'branch_id'             => 'Branch id',
        'nickname_en'           => 'Nickname in English',
        'nickname_ar'           => 'Nickname in Arabic',
        'specification_id'      => 'specification id',
        'price'                 => 'Price',
        'information_en'        => 'Information in English',
        'information_ar'        => 'Information in Arabic',
        'photo'                 => 'Photo',
        'insurance_companies'   => 'Insurance Companies',
        'working_days'          => 'Working Days',
        'nationality_id'        => 'Nationality ID',
        'reservation_period'    => 'Reservation Period',
        'commercial_no'         => 'Commercial Number',
        'nickname_id'           => 'Nickname ID',
        'agreement_ar'          => 'Agreement in Arabic',
        'agreement_en'          => 'Agreement in English',
        'user_id'               => 'User ID',
        'reservation_id'        => 'Reservation ID',
        'attachments'           => 'Attachments',
        'summary'               => 'Summary',
        'mobile_id'             => 'Mobile or Id Number',
        'doctor_rate'           => 'Doctor Rate',
        'provider_rate'         => 'Provider Rate',
        'message_id'            => 'Message ID',
        'type_id'               => 'Type ID',
        'hide'                  => 'Hide status',
        'insurance_expire_date' => 'Insurance Expire Date',
        'old_password'          => 'Current Password',
        'invited_user_mobile'   => 'phone number؛',
        'current mobile'        =>  'current mobile',
         'points'                => 'Points',
        'academy_id'             => 'Academy '

    ],

];
