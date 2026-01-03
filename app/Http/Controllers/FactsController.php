<?php
/**
 * 2026 DeveMain
 *
 * All rights reserved. For internal use only.
 * Unauthorized copying, modification, or distribution is prohibited.
 *
 * @author    DeveMain <devemain@gmail.com>
 * @copyright 2026 DeveMain
 * @license   PROPRIETARY
 * @link      https://github.com/DeveMain
 */

namespace App\Http\Controllers;

use App\Models\Fact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * Handles all operations related to facts display, statistics, and searching.
 */
class FactsController extends Controller
{
    /**
     * Default number of items to display per page.
     */
    protected int $defaultPerPage = 20;

    /**
     * Maximum limit for recent items display.
     */
    protected int $limit = 10;

    /**
     * Available options for items per page selection.
     */
    protected array $perPageOptions = [10, 20, 50, 100];

    /**
     * Display a paginated list of all facts.
     *
     * @param Request $request The incoming HTTP request containing pagination parameters
     * @return View The view displaying the facts list
     */
    public function index(Request $request): View
    {
        // Get the number of items per page from request, defaulting to class default if not valid
        $perPage = $request->integer('per_page', $this->defaultPerPage);
        if (!in_array($perPage, $this->perPageOptions)) {
            $perPage = $this->defaultPerPage;
        }

        // Get current page number from request, defaulting to 1
        $currentPage = $request->integer('page', 1);

        // Retrieve paginated facts, ordered by latest ID
        $facts = Fact::query()
            ->latest('id')
            ->paginate($perPage, ['*'], 'page', $currentPage);

        // Get total count of all facts for display purposes
        $totalFacts = Fact::query()->count();

        // Return the view with facts, total count, and current per page setting
        return view('facts.index', compact('facts', 'totalFacts', 'perPage'))
            ->with('perPageOptions', $this->perPageOptions);
    }

    /**
     * Display statistics about facts.
     *
     * @return View The view displaying facts statistics
     */
    public function stats(): View
    {
        // Get total count of all facts
        $total = Fact::query()->count();

        // Get count of facts created today
        $today = Fact::query()->whereDate('created_at', Carbon::today())->count();

        // Get count of facts created yesterday
        $yesterday = Fact::query()->whereDate('created_at', Carbon::yesterday())->count();

        $now = Carbon::now();

        // Get count of facts created in current month
        $thisMonth = Fact::query()
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        // Get the most recent facts limited by limit option
        $recent = Fact::query()->latest('id')->limit($this->limit)->get();

        // Return the view with all statistics data
        return view('facts.stats', compact(
            'total',
            'today',
            'yesterday',
            'thisMonth',
            'recent'
        ));
    }

    /**
     * Search facts based on query string.
     *
     * @param Request $request The incoming HTTP request containing search parameters
     * @return View|RedirectResponse The search results view or redirect to index if no query
     */
    public function search(Request $request): View|RedirectResponse
    {
        // Get and trim the search query from request
        $query = trim((string) $request->get('q', ''));

        // Redirect to index if query is empty
        if (empty($query)) {
            return redirect()->route('facts.index');
        }

        // Search facts by content containing the query string
        $facts = Fact::query()->where('content', 'like', '%' . $query . '%')
            ->latest('id')
            ->paginate($this->defaultPerPage);

        // Return the search results view
        return view('facts.search', compact('facts', 'query'));
    }
}
