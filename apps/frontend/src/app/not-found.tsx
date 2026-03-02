"use client";

import Link from "next/link";

export default function NotFound() {
  return (
    <div className="flex min-h-screen items-center justify-center bg-gray-50 dark:bg-gray-900 px-4">
      <div className="text-center">
        <h1 className="text-9xl font-bold text-gray-200 dark:text-gray-800">404</h1>
        <div className="mx-auto mb-6 mt-4 flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-red-500 to-orange-500 shadow-lg">
          <i className="fas fa-search text-4xl text-white"></i>
        </div>
        <h2 className="mb-2 text-2xl font-bold text-gray-900 dark:text-white">
          Pagina non trovata
        </h2>
        <p className="mb-8 text-gray-600 dark:text-gray-400">
          La risorsa che stai cercando non esiste o è stata spostata.
        </p>
        <div className="flex justify-center gap-4">
          <Link
            href="/"
            className="inline-flex items-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-3 text-sm font-medium text-white shadow-md hover:from-blue-600 hover:to-blue-700 transition-all"
          >
            <i className="fas fa-home mr-2"></i>
            Torna alla Home
          </Link>
        </div>
      </div>
    </div>
  );
}
