// src/components/MainContent.tsx
import React from 'react';
import styles from './MainContent.module.scss'; // Import styles

interface MainContentProps {
  children?: React.ReactNode;
}

const MainContent: React.FC<MainContentProps> = ({ children }) => {
  return (
    <main className={styles.mainContent}> {/* Apply style */}
      {children}
    </main>
  );
};
export default MainContent;
