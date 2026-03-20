'use client';

import { useState } from 'react';
import Navbar from '@/components/Navbar';
import ProductCard from '@/components/ProductCard';
import CartDrawer from '@/components/CartDrawer';
import CartSyncHandler from '@/components/CartSyncHandler';
import { useGetProductsQuery } from '@/lib/redux/api/apiSlice';
import { Loader2, Sparkles, MoveRight } from 'lucide-react';
import { motion } from 'framer-motion';

export default function Home() {
  const [isCartOpen, setIsCartOpen] = useState(false);
  const { data: productsData, isLoading, error } = useGetProductsQuery(undefined);

  const products = productsData?.data || [];

  return (
    <main className="min-h-screen bg-[#fafafa] pt-32 pb-20">
      <CartSyncHandler />
      <Navbar onCartClick={() => setIsCartOpen(true)} />
      <CartDrawer isOpen={isCartOpen} onClose={() => setIsCartOpen(false)} />

      <div className="max-w-7xl mx-auto px-6 md:px-12">
        {/* Hero Section */}
        <section id="featured" className="relative mb-24 rounded-[3rem] bg-gray-900 overflow-hidden min-h-[400px] flex items-center px-12 text-white">
          <div className="absolute inset-0 z-0">
            <div className="absolute top-0 right-0 w-1/2 h-full bg-indigo-600/20 blur-[120px] rounded-full" />
            <div className="absolute bottom-0 left-0 w-1/3 h-full bg-purple-600/10 blur-[100px] rounded-full" />
          </div>

          <div className="relative z-10 max-w-2xl">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              className="flex items-center gap-2 text-indigo-400 font-bold tracking-widest text-xs uppercase mb-6"
            >
              <Sparkles size={14} />
              Featured Collection 2024
            </motion.div>
            <motion.h1
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.1 }}
              className="text-5xl md:text-7xl font-outfit font-black mb-8 leading-[1.1]"
            >
              Elevate Your <br />
              <span className="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Digital Lifestyle</span>
            </motion.h1>
            <motion.p
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.2 }}
              className="text-gray-400 text-lg mb-10 max-w-md leading-relaxed"
            >
              Discover our curated selection of ultra-premium electronics and accessories, designed for the modern professional.
            </motion.p>
            <motion.button
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.3 }}
              className="group bg-indigo-600 hover:bg-indigo-500 text-white px-8 py-4 rounded-2xl font-bold flex items-center gap-3 transition-all"
            >
              Explore Now
              <MoveRight className="group-hover:translate-x-1 transition-transform" />
            </motion.button>
          </div>
        </section>

        {/* Products Grid */}
        <section id="shop">
          <div className="flex items-end justify-between mb-12">
            <div>
              <h2 className="text-3xl font-outfit font-black text-gray-900 mb-2">Editor's Choice</h2>
              <div className="h-1.5 w-12 bg-indigo-600 rounded-full" />
            </div>
            <p className="text-gray-500 font-medium">Showing {products.length} products</p>
          </div>

          {isLoading ? (
            <div className="h-64 flex flex-col items-center justify-center gap-4">
              <Loader2 className="animate-spin text-indigo-600" size={40} />
              <p className="text-gray-400 font-medium font-outfit">Loading our premium catalog...</p>
            </div>
          ) : error ? (
            <div className="h-64 flex flex-col items-center justify-center text-center">
              <p className="text-red-500 font-bold text-xl mb-2">Oops! Something went wrong.</p>
              <p className="text-gray-500">Failed to load products. Please check if the backend API is running.</p>
            </div>
          ) : products.length === 0 ? (
            <div className="h-64 flex flex-col items-center justify-center text-center">
              <p className="text-gray-900 font-bold text-xl mb-2">No products found.</p>
              <p className="text-gray-500">Check back later for new arrivals.</p>
            </div>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
              {Array.isArray(products) && products.map((product: any) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>
          )}
        </section>
      </div>

      {/* Luxury Footer */}
      <footer className="mt-40 border-t border-gray-100 pt-20 pb-10 px-6 md:px-12 bg-white">
        <div className="max-w-7xl mx-auto flex flex-col md:flex-row justify-between gap-12">
          <div className="max-w-sm">
            <div className="flex items-center gap-2 mb-6">
              <div className="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">P</div>
              <span className="font-outfit font-bold text-xl">PremiumCart</span>
            </div>
            <p className="text-gray-500 leading-relaxed mb-6">
              The world's most advanced shopping experience, seamlessly synchronizing across all your devices.
            </p>
          </div>

            <div className="grid grid-cols-2 gap-20">
              <div>
                <h4 className="font-bold text-gray-900 mb-6 uppercase tracking-widest text-xs">Navigation</h4>
                <ul className="space-y-4 text-gray-500 text-sm font-medium">
                  <li><a href="#shop" className="hover:text-indigo-600 transition-colors cursor-pointer">Shop</a></li>
                  <li><a href="#" className="hover:text-indigo-600 transition-colors cursor-pointer">Categories</a></li>
                  <li><a href="#featured" className="hover:text-indigo-600 transition-colors cursor-pointer">Featured</a></li>
                </ul>
              </div>
              <div>
                <h4 className="font-bold text-gray-900 mb-6 uppercase tracking-widest text-xs">Account</h4>
                <ul className="space-y-4 text-gray-500 text-sm font-medium">
                  <li className="hover:text-indigo-600 transition-colors cursor-pointer">Profile</li>
                  <li className="hover:text-indigo-600 transition-colors cursor-pointer">My Orders</li>
                  <li className="hover:text-indigo-600 transition-colors cursor-pointer">Wishlist</li>
                </ul>
              </div>
            </div>
        </div>
        <div className="max-w-7xl mx-auto mt-20 pt-8 border-t border-gray-50 flex flex-col md:flex-row justify-between items-center gap-6">
          <p className="text-gray-400 text-xs">© 2024 PremiumCart. Technical Assessment Project.</p>
          <div className="flex gap-8 text-gray-400 text-xs font-bold uppercase tracking-widest">
            <span className="hover:text-indigo-600 cursor-pointer transition-colors">Privacy</span>
            <span className="hover:text-indigo-600 cursor-pointer transition-colors">Terms</span>
          </div>
        </div>
      </footer>
    </main>
  );
}
