// src/components/Header.tsx
import React from 'react';
import styles from './Header.module.scss'; // Import styles

const Header: React.FC = () => {
  return (
    <header className={styles.siteHeader}> {/* Apply style */}
      <div className="container"> {/* Using global .container for now */}
        <h1>CrediCarbSf: Maximize Your Carbon Credit Value in Brazil's Leading Marketplace</h1>
        <p>Unlock seamless transactions and real-time insights. Join the future of carbon credit trading.</p>
        {/* Placeholder for high-impact visual (e.g., image/banner related to sustainability & finance) */}
      </div>
    </header>
  );
};
export default Header;
