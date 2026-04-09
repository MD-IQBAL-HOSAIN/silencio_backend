<?php
namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {

        $total_users       = User::count();
        $total_preferences = \App\Models\Preference::where('status', 'active')->count();
        $total_meals       = \App\Models\Meal::where('status', 'active')->count();
        $total_groceries   = \App\Models\GroceryType::where('status', 'active')->count();

        /* --- User chart data  start --- */
        $newUsers = User::whereYear('created_at', now()->year)
            ->get();

        // Define all months of the year
        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];

        /* --- User chart data  Start --- */

        // Initialize all months with 0
        $userCountsByMonth = array_fill_keys($months, 0);

        // Group the users by the month they were created
        $usersGroupedByMonth = $newUsers->groupBy(function ($user) {
            return $user->created_at->format('F'); // Group by month name
        });

        // Populate the count of users in the correct month
        foreach ($usersGroupedByMonth as $month => $users) {
            $userCountsByMonth[$month] = count($users);
        }

        // Prepare chart data
        $chartData = [
            'labels' => $months,
            'data'   => array_values($userCountsByMonth), // 12 values, all integers
        ];
        // dd($chartData);
        /* --- User chart data  end--- */





        /* Total meal chart data start */
        $totalMeals = \App\Models\Meal::where('status', 'active')->whereYear('created_at', now()->year)->get();

        // Initialize all months with 0
        $mealCountsByMonth = array_fill_keys($months, 0);

        // Group the meals by the month they were created
        $mealsGroupedByMonth = $totalMeals->groupBy(function ($meal) {
            return $meal->created_at->format('F'); // Group by month name
        });

        // Populate the count of meals in the correct month
        foreach ($mealsGroupedByMonth as $month => $meals) {
            $mealCountsByMonth[$month] = count($meals);
        }

        // Prepare chart data
        $mealChartData = [
            'labels' => $months,
            'data'   => array_values($mealCountsByMonth), // 12 values, all integers
        ];
        /* Total meal chart data end */

        return view('backend.dashboard', compact('total_users', 'chartData', 'mealChartData', 'total_preferences', 'total_meals', 'total_groceries'));
    }
}
