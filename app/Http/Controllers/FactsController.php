<?php
/**
 * 2025 DeveMain
 *
 * All rights reserved. For internal use only.
 * Unauthorized copying, modification, or distribution is prohibited.
 *
 * @author    DeveMain <devemain@gmail.com>
 * @copyright 2025 DeveMain
 * @license   PROPRIETARY
 * @link      https://github.com/DeveMain
 */

namespace App\Http\Controllers;

use App\Models\Fact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class FactsController extends Controller
{
    /**
     * Display a paginated list of all facts
     */
    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 20);
        $currentPage = $request->get('page', 1);

        $facts = Fact::query()
            ->latest('id')
            ->paginate($perPage, ['*'], 'page', $currentPage);

        $totalFacts = Fact::query()->count();

        return view('facts.index', compact('facts', 'totalFacts', 'perPage'));
    }

    /**
     * Display statistics about facts
     */
    public function stats(): View
    {
        $total = Fact::query()->count();
        $today = Fact::query()->whereDate('created_at', Carbon::today())->count();
        $yesterday = Fact::query()->whereDate('created_at', Carbon::yesterday())->count();
        $thisMonth = Fact::query()
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->count();

        $recent = Fact::query()->latest('id')->limit(10)->get();

        return view('facts.stats', compact(
            'total',
            'today',
            'yesterday',
            'thisMonth',
            'recent'
        ));
    }

    /**
     * Search facts
     */
    public function search(Request $request): View|RedirectResponse
    {
        $query = trim($request->get('q', ''));

        if (!$query) {
            return redirect()->route('facts.index');
        }

        $facts = Fact::query()->where('content', 'like', '%' . $query . '%')
            ->latest('id')
            ->paginate(20);

        return view('facts.search', [
            'facts' => $facts,
            'query' => $query,
        ]);
    }
}
