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

    'accepted' => ':attribute ফিল্ডটি অবশ্যই গৃহীত হতে হবে।',
    'accepted_if' => ':attribute ফিল্ডটি অবশ্যই গৃহীত হতে হবে যখন :other :value হয়।',
    'active_url' => ':attribute ফিল্ডটি অবশ্যই একটি বৈধ URL হতে হবে।',
    'after' => ':attribute ফিল্ডটি অবশ্যই :date-এর পরের তারিখ হতে হবে।',
    'after_or_equal' => ':attribute ফিল্ডটি অবশ্যই :date-এর পরের বা সমান তারিখ হতে হবে।',
    'alpha' => ':attribute ফিল্ডে শুধুমাত্র অক্ষর থাকতে হবে।',
    'alpha_dash' => ':attribute ফিল্ডে শুধুমাত্র অক্ষর, সংখ্যা, ড্যাশ এবং আন্ডারস্কোর থাকতে হবে।',
    'alpha_num' => ':attribute ফিল্ডে শুধুমাত্র অক্ষর এবং সংখ্যা থাকতে হবে।',
    'any_of' => ':attribute ফিল্ডটি অবৈধ।',
    'array' => ':attribute ফিল্ডটি অবশ্যই একটি অ্যারে হতে হবে।',
    'ascii' => ':attribute ফিল্ডে শুধুমাত্র একক-বাইট আলফানিউমেরিক অক্ষর এবং প্রতীক থাকতে হবে।',
    'before' => ':attribute ফিল্ডটি অবশ্যই :date-এর আগের তারিখ হতে হবে।',
    'before_or_equal' => ':attribute ফিল্ডটি অবশ্যই :date-এর আগের বা সমান তারিখ হতে হবে।',
    'between' => [
        'array' => ':attribute ফিল্ডে অবশ্যই :min এবং :max আইটেমের মধ্যে থাকতে হবে।',
        'file' => ':attribute ফিল্ডটি অবশ্যই :min এবং :max কিলোবাইটের মধ্যে হতে হবে।',
        'numeric' => ':attribute ফিল্ডটি অবশ্যই :min এবং :max-এর মধ্যে হতে হবে।',
        'string' => ':attribute ফিল্ডটি অবশ্যই :min এবং :max অক্ষরের মধ্যে হতে হবে।',
    ],
    'boolean' => ':attribute ফিল্ডটি অবশ্যই সত্য বা মিথ্যা হতে হবে।',
    'can' => ':attribute ফিল্ডে একটি অননুমোদিত মান রয়েছে।',
    'confirmed' => ':attribute ফিল্ড কনফার্মেশন মেলে না।',
    'contains' => ':attribute ফিল্ডে একটি প্রয়োজনীয় মান অনুপস্থিত।',
    'current_password' => 'পাসওয়ার্ডটি ভুল।',
    'date' => ':attribute ফিল্ডটি অবশ্যই একটি বৈধ তারিখ হতে হবে।',
    'date_equals' => ':attribute ফিল্ডটি অবশ্যই :date-এর সমান তারিখ হতে হবে।',
    'date_format' => ':attribute ফিল্ডটি অবশ্যই :format ফরম্যাটের সাথে মিলতে হবে।',
    'decimal' => ':attribute ফিল্ডে অবশ্যই :decimal দশমিক স্থান থাকতে হবে।',
    'declined' => ':attribute ফিল্ডটি অবশ্যই প্রত্যাখ্যান করতে হবে।',
    'declined_if' => ':attribute ফিল্ডটি অবশ্যই প্রত্যাখ্যান করতে হবে যখন :other :value হয়।',
    'different' => ':attribute ফিল্ড এবং :other অবশ্যই ভিন্ন হতে হবে।',
    'digits' => ':attribute ফিল্ডটি অবশ্যই :digits অঙ্ক হতে হবে।',
    'digits_between' => ':attribute ফিল্ডটি অবশ্যই :min এবং :max অঙ্কের মধ্যে হতে হবে।',
    'dimensions' => ':attribute ফিল্ডে অবৈধ ছবির মাত্রা রয়েছে।',
    'distinct' => ':attribute ফিল্ডের একটি সদৃশ মান আছে।',
    'doesnt_end_with' => ':attribute ফিল্ডটি নিম্নলিখিতগুলির মধ্যে একটি দিয়ে শেষ হওয়া উচিত নয়: :values.',
    'doesnt_start_with' => ':attribute ফিল্ডটি নিম্নলিখিতগুলির মধ্যে একটি দিয়ে শুরু হওয়া উচিত নয়: :values.',
    'email' => ':attribute ফিল্ডটি অবশ্যই একটি বৈধ ইমেল ঠিকানা হতে হবে।',
    'ends_with' => ':attribute ফিল্ডটি অবশ্যই নিম্নলিখিতগুলির মধ্যে একটি দিয়ে শেষ হতে হবে: :values.',
    'enum' => 'নির্বাচিত :attribute অবৈধ।',
    'exists' => 'নির্বাচিত :attribute অবৈধ।',
    'extensions' => ':attribute ফিল্ডে নিম্নলিখিত এক্সটেনশনগুলির মধ্যে একটি থাকতে হবে: :values.',
    'file' => ':attribute ফিল্ডটি অবশ্যই একটি ফাইল হতে হবে।',
    'filled' => ':attribute ফিল্ডের একটি মান থাকতে হবে।',
    'gt' => [
        'array' => ':attribute ফিল্ডে অবশ্যই :value-এর বেশি আইটেম থাকতে হবে।',
        'file' => ':attribute ফিল্ডটি অবশ্যই :value কিলোবাইটের চেয়ে বড় হতে হবে।',
        'numeric' => ':attribute ফিল্ডটি অবশ্যই :value-এর চেয়ে বড় হতে হবে।',
        'string' => ':attribute ফিল্ডটি অবশ্যই :value অক্ষরের চেয়ে বড় হতে হবে।',
    ],
    'gte' => [
        'array' => ':attribute ফিল্ডে অবশ্যই :value আইটেম বা তার বেশি থাকতে হবে।',
        'file' => ':attribute ফিল্ডটি অবশ্যই :value কিলোবাইটের চেয়ে বড় বা সমান হতে হবে।',
        'numeric' => ':attribute ফিল্ডটি অবশ্যই :value-এর চেয়ে বড় বা সমান হতে হবে।',
        'string' => ':attribute ফিল্ডটি অবশ্যই :value অক্ষরের চেয়ে বড় বা সমান হতে হবে।',
    ],
    'hex_color' => ':attribute ফিল্ডটি অবশ্যই একটি বৈধ হেক্সাডেসিমেল রঙ হতে হবে।',
    'image' => ':attribute ফিল্ডটি অবশ্যই একটি ছবি হতে হবে।',
    'in' => 'নির্বাচিত :attribute অবৈধ।',
    'in_array' => ':attribute ফিল্ডটি অবশ্যই :other-এ বিদ্যমান থাকতে হবে।',
    'in_array_keys' => ':attribute ফিল্ডে নিম্নলিখিত কীগুলির মধ্যে অন্তত একটি থাকতে হবে: :values.',
    'integer' => ':attribute ফিল্ডটি অবশ্যই একটি পূর্ণসংখ্যা হতে হবে।',
    'ip' => ':attribute ফিল্ডটি অবশ্যই একটি বৈধ IP ঠিকানা হতে হবে।',
    'ipv4' => ':attribute ফিল্ডটি অবশ্যই একটি বৈধ IPv4 ঠিকানা হতে হবে।',
    'ipv6' => ':attribute ফিল্ডটি অবশ্যই একটি বৈধ IPv6 ঠিকানা হতে হবে।',
    'json' => ':attribute ফিল্ডটি অবশ্যই একটি বৈধ JSON স্ট্রিং হতে হবে।',
    'list' => ':attribute ফিল্ডটি অবশ্যই একটি তালিকা হতে হবে।',
    'lowercase' => ':attribute ফিল্ডটি অবশ্যই ছোট হাতের হতে হবে।',
    'lt' => [
        'array' => ':attribute ফিল্ডে অবশ্যই :value-এর কম আইটেম থাকতে হবে।',
        'file' => ':attribute ফিল্ডটি অবশ্যই :value কিলোবাইটের চেয়ে কম হতে হবে।',
        'numeric' => ':attribute ফিল্ডটি অবশ্যই :value-এর চেয়ে কম হতে হবে।',
        'string' => ':attribute ফিল্ডটি অবশ্যই :value অক্ষরের চেয়ে কম হতে হবে।',
    ],
    'lte' => [
        'array' => ':attribute ফিল্ডে :value-এর বেশি আইটেম থাকা উচিত নয়।',
        'file' => ':attribute ফিল্ডটি অবশ্যই :value কিলোবাইটের চেয়ে কম বা সমান হতে হবে।',
        'numeric' => ':attribute ফিল্ডটি অবশ্যই :value-এর চেয়ে কম বা সমান হতে হবে।',
        'string' => ':attribute ফিল্ডটি অবশ্যই :value অক্ষরের চেয়ে কম বা সমান হতে হবে।',
    ],
    'mac_address' => ':attribute ফিল্ডটি অবশ্যই একটি বৈধ MAC ঠিকানা হতে হবে।',
    'max' => [
        'array' => ':attribute ফিল্ডে :max-এর বেশি আইটেম থাকা উচিত নয়।',
        'file' => ':attribute ফিল্ডটি :max কিলোবাইটের চেয়ে বড় হওয়া উচিত নয়।',
        'numeric' => ':attribute ফিল্ডটি :max-এর চেয়ে বড় হওয়া উচিত নয়।',
        'string' => ':attribute ফিল্ডটি :max অক্ষরের চেয়ে বড় হওয়া উচিত নয়।',
    ],
    'max_digits' => ':attribute ফিল্ডে :max-এর বেশি অঙ্ক থাকা উচিত নয়।',
    'mimes' => ':attribute ফিল্ডটি অবশ্যই একটি :values ধরনের ফাইল হতে হবে।',
    'mimetypes' => ':attribute ফিল্ডটি অবশ্যই একটি :values ধরনের ফাইল হতে হবে।',
    'min' => [
        'array' => ':attribute ফিল্ডে কমপক্ষে :min আইটেম থাকতে হবে।',
        'file' => ':attribute ফিল্ডটি কমপক্ষে :min কিলোবাইট হতে হবে।',
        'numeric' => ':attribute ফিল্ডটি কমপক্ষে :min হতে হবে।',
        'string' => ':attribute ফিল্ডটি কমপক্ষে :min অক্ষর হতে হবে।',
    ],
    'min_digits' => ':attribute ফিল্ডে কমপক্ষে :min অঙ্ক থাকতে হবে।',
    'missing' => ':attribute ফিল্ডটি অনুপস্থিত থাকতে হবে।',
    'missing_if' => ':attribute ফিল্ডটি অনুপস্থিত থাকতে হবে যখন :other :value হয়।',
    'missing_unless' => ':attribute ফিল্ডটি অনুপস্থিত থাকতে হবে যদি না :other :value হয়।',
    'missing_with' => ':attribute ফিল্ডটি অনুপস্থিত থাকতে হবে যখন :values উপস্থিত থাকে।',
    'missing_with_all' => ':attribute ফিল্ডটি অনুপস্থিত থাকতে হবে যখন :values উপস্থিত থাকে।',
    'multiple_of' => ':attribute ফিল্ডটি অবশ্যই :value-এর গুণিতক হতে হবে।',
    'not_in' => 'নির্বাচিত :attribute অবৈধ।',
    'not_regex' => ':attribute ফিল্ড ফরম্যাটটি অবৈধ।',
    'numeric' => ':attribute ফিল্ডটি অবশ্যই একটি সংখ্যা হতে হবে।',
    'password' => [
        'letters' => ':attribute ফিল্ডে কমপক্ষে একটি অক্ষর থাকতে হবে।',
        'mixed' => ':attribute ফিল্ডে কমপক্ষে একটি বড় হাতের এবং একটি ছোট হাতের অক্ষর থাকতে হবে।',
        'numbers' => ':attribute ফিল্ডে কমপক্ষে একটি সংখ্যা থাকতে হবে।',
        'symbols' => ':attribute ফিল্ডে কমপক্ষে একটি প্রতীক থাকতে হবে।',
        'uncompromised' => 'প্রদত্ত :attribute একটি ডেটা ফাঁসে উপস্থিত হয়েছে। অনুগ্রহ করে একটি ভিন্ন :attribute চয়ন করুন।',
    ],
    'present' => ':attribute ফিল্ডটি অবশ্যই উপস্থিত থাকতে হবে।',
    'present_if' => ':attribute ফিল্ডটি অবশ্যই উপস্থিত থাকতে হবে যখন :other :value হয়।',
    'present_unless' => ':attribute ফিল্ডটি অবশ্যই উপস্থিত থাকতে হবে যদি না :other :value হয়।',
    'present_with' => ':attribute ফিল্ডটি অবশ্যই উপস্থিত থাকতে হবে যখন :values উপস্থিত থাকে।',
    'present_with_all' => ':attribute ফিল্ডটি অবশ্যই উপস্থিত থাকতে হবে যখন :values উপস্থিত থাকে।',
    'prohibited' => ':attribute ফিল্ডটি নিষিদ্ধ।',
    'prohibited_if' => ':attribute ফিল্ডটি নিষিদ্ধ যখন :other :value হয়।',
    'prohibited_if_accepted' => ':attribute ফিল্ডটি নিষিদ্ধ যখন :other গৃহীত হয়।',
    'prohibited_if_declined' => ':attribute ফিল্ডটি নিষিদ্ধ যখন :other প্রত্যাখ্যান করা হয়।',
    'prohibited_unless' => ':attribute ফিল্ডটি নিষিদ্ধ যদি না :other :values-এ থাকে।',
    'prohibits' => ':attribute ফিল্ড :other-কে উপস্থিত হতে বাধা দেয়।',
    'regex' => ':attribute ফিল্ড ফরম্যাটটি অবৈধ।',
    'required' => ':attribute ফিল্ডটি প্রয়োজনীয়।',
    'required_array_keys' => ':attribute ফিল্ডে অবশ্যই এন্ট্রি থাকতে হবে: :values.',
    'required_if' => ':attribute ফিল্ডটি প্রয়োজনীয় যখন :other :value হয়।',
    'required_if_accepted' => ':attribute ফিল্ডটি প্রয়োজনীয় যখন :other গৃহীত হয়।',
    'required_if_declined' => ':attribute ফিল্ডটি প্রয়োজনীয় যখন :other প্রত্যাখ্যান করা হয়।',
    'required_unless' => ':attribute ফিল্ডটি প্রয়োজনীয় যদি না :other :values-এ থাকে।',
    'required_with' => ':attribute ফিল্ডটি প্রয়োজনীয় যখন :values উপস্থিত থাকে।',
    'required_with_all' => ':attribute ফিল্ডটি প্রয়োজনীয় যখন :values উপস্থিত থাকে।',
    'required_without' => ':attribute ফিল্ডটি প্রয়োজনীয় যখন :values উপস্থিত না থাকে।',
    'required_without_all' => ':attribute ফিল্ডটি প্রয়োজনীয় যখন :values-এর কোনোটিই উপস্থিত না থাকে।',
    'same' => ':attribute ফিল্ডটি অবশ্যই :other-এর সাথে মিলতে হবে।',
    'size' => [
        'array' => ':attribute ফিল্ডে অবশ্যই :size আইটেম থাকতে হবে।',
        'file' => ':attribute ফিল্ডটি অবশ্যই :size কিলোবাইট হতে হবে।',
        'numeric' => ':attribute ফিল্ডটি অবশ্যই :size হতে হবে।',
        'string' => ':attribute ফিল্ডটি অবশ্যই :size অক্ষর হতে হবে।',
    ],
    'starts_with' => ':attribute ফিল্ডটি অবশ্যই নিম্নলিখিতগুলির মধ্যে একটি দিয়ে শুরু হতে হবে: :values.',
    'string' => ':attribute ফিল্ডটি অবশ্যই একটি স্ট্রিং হতে হবে।',
    'timezone' => ':attribute ফিল্ডটি অবশ্যই একটি বৈধ টাইমজোন হতে হবে।',
    'unique' => ':attribute ইতিমধ্যে নেওয়া হয়েছে।',
    'uploaded' => ':attribute আপলোড করতে ব্যর্থ হয়েছে।',
    'uppercase' => ':attribute ফিল্ডটি অবশ্যই বড় হাতের হতে হবে।',
    'url' => ':attribute ফিল্ডটি অবশ্যই একটি বৈধ URL হতে হবে।',
    'ulid' => ':attribute ফিল্ডটি অবশ্যই একটি বৈধ ULID হতে হবে।',
    'uuid' => ':attribute ফিল্ডটি অবশ্যই একটি বৈধ UUID হতে হবে।',

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

    'attributes' => include "values.php",

];
