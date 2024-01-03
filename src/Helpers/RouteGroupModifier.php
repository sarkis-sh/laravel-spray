<?php

declare(strict_types=1);

namespace src\Helpers;


/**
 * Class RouteGroupModifier
 *
 * Provides methods for modifying route groups in Laravel routes file.
 *
 * @package src\Helpers
 */
class RouteGroupModifier
{
    /**
     * Appends a new route to an existing route group in a routes file.
     *
     * @param string $routeFileContents The contents of the routes file.
     * @param string $prefix The route group prefix.
     * @param string $newRoute The new route to append.
     * @return string The updated contents of the routes file if the route was successfully appended, or the original contents if there were errors.
     */
    public static function appendToExistingRouteGroup(string $routeFileContents, string $prefix, string $newRoute): string
    {
        // Define the regex pattern to match the route group identifier
        $groupIdentifierPattern = "/Route::group\(\[\s*'prefix'\s*=>\s*'\/$prefix'\s*,[^)]+\)\s*{/s";

        // Find the first occurrence of the route group identifier in the file contents
        preg_match($groupIdentifierPattern, $routeFileContents, $matches);

        // If no matches are found, the group routes section is not found
        if (empty($matches)) {
            return $routeFileContents;
        }
        // Store the matched group identifier
        $groupIdentifier = $matches[0];

        // Define the regex pattern to match the closing bracket of the group routes section
        $endBracketPattern = "/\n\s*}\);/s";

        // Find the closing bracket of the group routes section
        preg_match($endBracketPattern, $routeFileContents, $matches, PREG_OFFSET_CAPTURE, strpos($routeFileContents, $groupIdentifier));

        // If no matches are found, the closing bracket is not found
        if (empty($matches)) {
            return $routeFileContents;
        }

        // Calculate the end position of the group routes section
        $endPos = $matches[0][1] + strlen($matches[0][0]);

        // Extract the existing group routes section
        $existingGroupRoutes = substr($routeFileContents, strpos($routeFileContents, $groupIdentifier), $endPos - strpos($routeFileContents, $groupIdentifier));

        // Check if the new route already exists in the group routes section
        $routeExists = strpos($existingGroupRoutes, $newRoute) !== false;

        // If the route already exists, return an error
        if ($routeExists) {
            return $routeFileContents;
        }

        // Append the new route to the existing group routes
        $updatedGroupRoutes = str_replace($groupIdentifier, $groupIdentifier . "\n" . $newRoute, $existingGroupRoutes);

        // Replace the existing group routes with the updated group routes in the file contents
        $updatedContents = str_replace($existingGroupRoutes, $updatedGroupRoutes, $routeFileContents);

        return $updatedContents;
    }
}
