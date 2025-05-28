// src/components/SellerBuyerTabs.tsx
import React, { useState } from 'react';
import styles from './SellerBuyerTabs.module.scss';

const SellerBuyerTabs: React.FC = () => {
  const [activeTab, setActiveTab] = useState<'sellers' | 'buyers'>('sellers');

  return (
    <section className={styles.tabsSection}>
      <div className="container">
        <h2>For Sellers & Buyers</h2>
        <div className={styles.tabsNavigation}>
          <button
            className={`${styles.tabButton} ${activeTab === 'sellers' ? styles.active : ''}`}
            onClick={() => setActiveTab('sellers')}
          >
            For Sellers
          </button>
          <button
            className={`${styles.tabButton} ${activeTab === 'buyers' ? styles.active : ''}`}
            onClick={() => setActiveTab('buyers')}
          >
            For Buyers
          </button>
        </div>
        <div className={styles.tabsContent}>
          {activeTab === 'sellers' && (
            <div className={styles.tabPane}>
              <h3>Carbon Credit Sellers</h3>
              <p>Maximize your revenue and connect with a wide network of buyers. Showcase your projects, get fair pricing, and contribute to a greener future. Our transparent process makes selling your carbon credits straightforward and efficient. Maximize your carbon credit value with real-time market insights!</p>
            </div>
          )}
          {activeTab === 'buyers' && (
            <div className={styles.tabPane}>
              <h3>Carbon Credit Buyers</h3>
              <p>Achieve your environmental targets with high-quality, verified carbon credits from Brazilian projects. Our platform offers a diverse portfolio of options to meet your specific needs, ensuring transparency and reliability in every transaction. Seamless transactions for buyers and sellers—secure, transparent, efficient.</p>
            </div>
          )}
        </div>
      </div>
    </section>
  );
};
export default SellerBuyerTabs;
