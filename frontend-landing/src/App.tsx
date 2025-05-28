// src/App.tsx
// import React from 'react'; // Removed as it's not strictly needed with modern JSX transform
import Header from './components/Header';
import Hero from './components/Hero';
import MainContent from './components/MainContent';
import ValueProposition from './components/ValueProposition';
import SellerBuyerTabs from './components/SellerBuyerTabs';
import CallToAction from './components/CallToAction';
import Footer from './components/Footer';
// import styles from './App.module.scss'; // If we add App specific styles

function App() {
  return (
    <div>
      <Header />
      <Hero />
      <MainContent>
        <ValueProposition />
        <SellerBuyerTabs />
        {/* Placeholder sections for Testimonials and Market Trends can be added here later if desired */}
        <CallToAction />
      </MainContent>
      <Footer />
    </div>
  );
}
export default App;
