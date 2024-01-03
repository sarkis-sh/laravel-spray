<?php

declare(strict_types=1);

namespace src\Constants;


/**
 * Class Faker
 *
 * This class contains constants for column fakers used in the application.
 * 
 * @package src\Constants
 */
class Faker
{
    /** @var array The mapping of column names to their corresponding faker expressions. */
    public const COLUMN_NAME_FAKER = [
        'password'              => 'bcrypt(\'123456\')',
        'email'                 => '$this->faker->email()',
        'userName'              => '$this->faker->userName()',
        'user_name'             => '$this->faker->userName()',
        'domain_name'           => '$this->faker->domainName()',
        'domainName'            => '$this->faker->domainName()',
        'dns'                   => '$this->faker->domainName()',
        'url'                   => '$this->faker->url()',
        'link'                  => '$this->faker->url()',
        'ipv4'                  => '$this->faker->ipv4()',
        'ipv6'                  => '$this->faker->ipv6()',
        'mac_address'           => '$this->faker->macAddress()',
        'macAddress'            => '$this->faker->macAddress()',
        'user_agent'            => '$this->faker->userAgent()',
        'userAgent'             => '$this->faker->userAgent()',
        'creditCardType'        => '$this->faker->creditCardType()',
        'credit_card_type'      => '$this->faker->creditCardType()',
        'creditCardNumber'      => '$this->faker->creditCardNumber()',
        'credit_card_number'    => '$this->faker->creditCardNumber()',
        'color'                 => '$this->faker->colorName()',
        'mime'                  => '$this->faker->mimeType()',
        'fileExtension'         => '$this->faker->fileExtension()',
        'file_extension'        => '$this->faker->fileExtension()',
        'image'                 => '$this->faker->imageUrl(640, 480)',
        'file'                  => '$this->faker->imageUrl(640, 480)',
        'uuid'                  => '$this->faker->uuid()',
        'md5'                   => '$this->faker->md5()',
        'sha1'                  => '$this->faker->sha1()',
        'sha256'                => '$this->faker->sha256()',
        'locale'                => '$this->faker->locale()',
        'localization'          => '$this->faker->locale()',
        'country_code'          => '$this->faker->countryCode()',
        'countryCode'           => '$this->faker->countryCode()',
        'lang'                  => '$this->faker->languageCode()',
        'language'              => '$this->faker->languageCode()',
        'currency'              => '$this->faker->currencyCode()',
        'emoji'                 => '$this->faker->emoji()',
        'html'                  => '$this->faker->randomHtml()',
        'name'                  => '$this->faker->name()',
        'firstName'             => '$this->faker->firstName()',
        'first_name'            => '$this->faker->firstName()',
        'lastName'              => '$this->faker->lastName()',
        'last_name'             => '$this->faker->lastName()',
        'state'                 => '$this->faker->state()',
        'city'                  => '$this->faker->city()',
        'street'                => '$this->faker->streetName()',
        'postcode'              => '$this->faker->postcode()',
        'post_code'             => '$this->faker->postcode()',
        'address'               => '$this->faker->address()',
        'country'               => '$this->faker->country()',
        'latitude'              => '$this->faker->latitude($min = -90, $max = 90)',
        'lat'                   => '$this->faker->latitude($min = -90, $max = 90)',
        'longitude'             => '$this->faker->longitude($min = -180, $max = 180)',
        'lng'                   => '$this->faker->longitude($min = -180, $max = 180)',
        'phone'                 => '$this->faker->e164PhoneNumber()',
        'phoneNumber'           => '$this->faker->e164PhoneNumber()',
        'phone_number'          => '$this->faker->e164PhoneNumber()',
        'mobileNumber'          => '$this->faker->e164PhoneNumber()',
        'mobile_number'         => '$this->faker->e164PhoneNumber()'
    ];
}
