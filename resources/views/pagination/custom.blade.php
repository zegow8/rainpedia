@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-top:20px; padding-top:16px; border-top:1px solid #e2e8f0; width:100%;">
        <div style="font-size:0.85rem; color:#64748b;">
            Menampilkan <strong style="color:#1e293b;">{{ $paginator->firstItem() ?? 0 }}</strong> sampai <strong style="color:#1e293b;">{{ $paginator->lastItem() ?? 0 }}</strong> dari <strong style="color:#1e293b;">{{ $paginator->total() }}</strong> data
        </div>

        <ul style="display:inline-flex; list-style:none; padding:0; margin:0; gap:4px; align-items:center; flex-wrap:wrap;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span style="display:inline-flex; align-items:center; justify-content:center; padding:6px 12px; font-size:0.82rem; font-weight:600; color:#cbd5e1; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; cursor:not-allowed;">
                        &laquo; Sebelumnya
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display:inline-flex; align-items:center; justify-content:center; padding:6px 12px; font-size:0.82rem; font-weight:600; color:#334155; background:white; border:1px solid #cbd5e1; border-radius:6px; text-decoration:none; transition:all 0.15s;">
                        &laquo; Sebelumnya
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li><span style="padding:6px 8px; font-size:0.82rem; color:#94a3b8;">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span style="display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 8px; font-size:0.82rem; font-weight:700; color:white; background:#2563eb; border:1px solid #2563eb; border-radius:6px;">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" style="display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 8px; font-size:0.82rem; font-weight:600; color:#475569; background:white; border:1px solid #cbd5e1; border-radius:6px; text-decoration:none; transition:all 0.15s;">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display:inline-flex; align-items:center; justify-content:center; padding:6px 12px; font-size:0.82rem; font-weight:600; color:#334155; background:white; border:1px solid #cbd5e1; border-radius:6px; text-decoration:none; transition:all 0.15s;">
                        Selanjutnya &raquo;
                    </a>
                </li>
            @else
                <li>
                    <span style="display:inline-flex; align-items:center; justify-content:center; padding:6px 12px; font-size:0.82rem; font-weight:600; color:#cbd5e1; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; cursor:not-allowed;">
                        Selanjutnya &raquo;
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
